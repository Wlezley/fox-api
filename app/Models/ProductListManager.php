<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Exception\ProductException;
use App\Models\Repository\ProductRepository;
use Nette\Http\Request;
use Nette\Http\Response;

final class ProductListManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get
    ];

    public function __construct(
        public Request $httpRequest,
        public Response $httpResponse,
        public ProductRepository $productRepo
    ) {}

    protected function processGET(): bool
    {
        // TODO: Validate the Query params !!!

        $filter = [];

        $nameFilter = $this->httpRequest->getQuery('name');
        if ($nameFilter) {
            $filter['name LIKE ?'] = "%$nameFilter%";
        }

        $minPrice = $this->httpRequest->getQuery('minPrice');
        $maxPrice = $this->httpRequest->getQuery('maxPrice');
        if ($minPrice && $maxPrice) {
            $filter['price BETWEEN ? AND ?'] = [$minPrice, $maxPrice];
        } elseif ($minPrice) {
            $filter['price >= ?'] = $minPrice;
        } elseif ($maxPrice) {
            $filter['price <= ?'] = $maxPrice;
        }

        $minStock = $this->httpRequest->getQuery('minStock');
        $maxStock = $this->httpRequest->getQuery('maxStock');
        if ($minStock && $maxStock) {
            $filter['stock BETWEEN ? AND ?'] = [$minStock, $maxStock];
        } elseif ($minStock) {
            $filter['stock >= ?'] = $minStock;
        } elseif ($maxStock) {
            $filter['stock <= ?'] = $maxStock;
        }

        $limit = $this->httpRequest->getQuery('limit') ?? 50;
        $offset = $this->httpRequest->getQuery('offset') ?? 0;

        try {
            $this->data = $this->productRepo->getList((int) $limit, (int) $offset, $filter);
            return true;
        } catch (ProductException $e) {
            $this->setError($e->getCode(), $e->getMessage());
        }

        return false;
    }
}
