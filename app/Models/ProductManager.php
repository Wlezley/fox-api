<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Entity\ProductEntity;
use App\Models\Exception\ProductException;
use App\Models\Repository\ProductRepository;
use App\Models\Validator\ProductInputValidator;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\InvalidArgumentException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * API manager for handling /product/ related REST operations.
 *
 * Supports GET, POST, PATCH, PUT and DELETE HTTP methods.
 */
final class ProductManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get,
        Request::Post,
        Request::Patch,
        Request::Put,
        Request::Delete
    ];

    /**
     * @param Request $httpRequest Current HTTP request
     * @param Response $httpResponse Current HTTP response
     * @param ProductRepository $productRepo Product repository instance
     */
    public function __construct(
        public Request $httpRequest,
        public Response $httpResponse,
        public ProductRepository $productRepo
    ) {}

    /**
     * Handles HTTP GET request to retrieve a product by ID.
     *
     * @return bool True on success, false on failure (e.g. invalid or missing ID)
     */
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

    /**
     * Handles HTTP POST request to insert a new product.
     *
     * Expects a valid JSON body with required product data.
     *
     * @return bool True on success, false on validation or JSON error
     */
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

        if (empty($data)) {
            $this->setError(Response::S400_BadRequest, "Empty input data");
            return false;
        }

        try {
            $validatedData = ProductInputValidator::validate($data);
        } catch (InvalidArgumentException $e) {
            $this->setError(Response::S400_BadRequest, $e->getMessage());
            return false;
        }

        $productData = ProductEntity::fromDatabaseRow($validatedData);
        $productData = $this->productRepo->insert($productData);
        $this->data = $productData->toArray();
        return true;
    }

    /**
     * Handles HTTP PATCH request to update a product partially.
     *
     * Requires a valid ID in query parameters and JSON body with updated fields.
     *
     * @return bool True on success, false on error
     */
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

    /**
     * Handles HTTP PUT request to insert or update a product.
     *
     * If the product with given ID exists, updates it (PATCH).
     * Otherwise, creates a new one (POST).
     *
     * @return bool True on success, false on error
     */
    protected function processPUT(): bool
    {
        $id = $this->httpRequest->getQuery('id');

        if (!$id || !$this->productRepo->exists($id)) {
            return $this->processPOST();
        } else {
            return $this->processPATCH();
        }
    }

    /**
     * Handles HTTP DELETE request to mark a product as deleted (soft delete).
     *
     * Requires 'id' query parameter.
     *
     * @return bool True on success, false on failure
     */
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
