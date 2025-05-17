<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Exception\ProductHistoryException;
use App\Models\Repository\ProductHistoryRepository;
use Nette\Http\Request;
use Nette\Http\Response;

final class ProductHistoryManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get
    ];

    public function __construct(
        public Request $httpRequest,
        public Response $httpResponse,
        public ProductHistoryRepository $productHistoryRepo
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
                $limit = $this->httpRequest->getQuery('limit') ?? 50;
                $offset = $this->httpRequest->getQuery('offset') ?? 0;
                $this->data = $this->productHistoryRepo
                    ->getHistoryByProductId((int) $id, (int) $limit, (int) $offset);
                return true;
            } catch (ProductHistoryException $e) {
                $this->setError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }
}
