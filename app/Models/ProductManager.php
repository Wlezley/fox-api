<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\ProductEntity;
use App\Models\Exception\ProductException;
use App\Models\Repository\ProductRepository;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

final class ProductManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get,
        Request::Post,
        Request::Patch,
        Request::Put,
        Request::Delete
    ];

    public function __construct(
        public Request $httpRequest,
        public Response $httpResponse,
        public ProductRepository $productRepo
    ) {}

    protected function processGET(): bool
    {
        $id = $this->httpRequest->getQuery('id');

        if (!$id) {
            $this->setError(
                Response::S400_BadRequest,
                "Required parameter 'id' is missing"
            );
            return false;
        } else {
            try {
                $productData = $this->productRepo->getProductById((int) $id);
                $this->data = $productData->toArray();
                return true;
            } catch (ProductException $e) {
                $this->setError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }

    protected function processPOST(): bool
    {
        try {
            $data = Json::decode($this->httpRequest->getRawBody(), true);
        } catch (JsonException $e) {
            $this->setError(
                Response::S400_BadRequest,
                "JSON Exception #{$e->getCode()}: {$e->getMessage()}"
            );
            return false;
        }

        // TODO: Validate post data values ...
        if (empty($data)) {
            $this->setError(
                Response::S400_BadRequest,
                "Empty input data"
            );
            return false;
        }

        $productData = ProductEntity::fromDatabaseRow($data);
        $productData = $this->productRepo->insert($productData);
        $this->data = $productData->toArray();
        return true;
    }

    protected function processPATCH(): bool
    {
        $id = $this->httpRequest->getQuery('id');

        if (!$id) {
            $this->setError(
                Response::S400_BadRequest,
                "Required parameter 'id' is missing"
            );
            return false;
        } else {
            try {
                $data = Json::decode($this->httpRequest->getRawBody(), true);
                $productData = $this->productRepo->getProductById((int) $id);
                $productData->prepareForUpdate($data);
                $productData = $this->productRepo->update($productData);
                $this->data = $productData->toArray();
                return true;
            } catch (JsonException $e) {
                $this->setError(
                    Response::S400_BadRequest,
                    "JSON Exception #{$e->getCode()}: {$e->getMessage()}"
                );
                return false;
            } catch (ProductException $e) {
                $this->setError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }

    protected function processPUT(): bool
    {
        $id = $this->httpRequest->getQuery('id');

        if (!$id || !$this->productRepo->exists($id)) {
            return $this->processPOST();
        } else {
            return $this->processPATCH();
        }
    }

    protected function processDELETE(): bool
    {
        $id = $this->httpRequest->getQuery('id');

        if (!$id) {
            $this->setError(
                Response::S400_BadRequest,
                "Required parameter 'id' is missing"
            );
            return false;
        } else {
            try {
                $this->data = [
                    'id' => $id,
                    'affected_rows' => $this->productRepo->setDeleted((int) $id, true)
                ];
                return true;
            } catch (ProductException $e) {
                $this->setError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }
}
