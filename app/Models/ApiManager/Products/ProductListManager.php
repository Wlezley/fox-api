<?php

declare(strict_types=1);

namespace App\Models\ApiManager;

use App\Models\ApiException\ProductException;
use App\Models\Repository\ProductRepository;
use App\Models\Validator\ProductFilterValidator;
use Nette\Http\Request;
use Nette\Http\Response;

/**
 * API manager for handling /products/ related REST operations.
 *
 * Supports only the HTTP GET method.
 */

final class ProductListManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get
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
     * Handles HTTP GET request to retrieve a filtered and paginated list of products.
     *
     * Filters supported: name, price range, stock range. All parameters are optional.
     * Uses default pagination if none is provided.
     *
     * Optional query parameters:
     * - name: (string) Filter by product name (partial match, case-insensitive)
     * - minPrice: (float) Minimum product price
     * - maxPrice: (float) Maximum product price
     * - minStock: (int) Minimum stock count
     * - maxStock: (int) Maximum stock count
     * - limit: (int) Maximum number of products to return (default: 50, max: 100)
     * - offset: (int) Number of products to skip (default: 0)
     *
     * @return bool True on success, false if an error occurs (e.g., invalid parameter or internal error)
     */
    protected function processGET(): bool
    {
        try {
            $query = ProductFilterValidator::getValidatedFilters($this->httpRequest->getQuery());
        } catch (\Nette\InvalidArgumentException $e) {
            $this->setError($e->getCode(), $e->getMessage());
            return false;
        }

        $filter = [];

        if ($query['name']) {
            $filter['name LIKE ?'] = "%{$query['name']}%";
        }

        if ($query['minPrice'] && $query['maxPrice']) {
            $filter['price BETWEEN ? AND ?'] = [$query['minPrice'], $query['maxPrice']];
        } elseif ($query['minPrice']) {
            $filter['price >= ?'] = $query['minPrice'];
        } elseif ($query['maxPrice']) {
            $filter['price <= ?'] = $query['maxPrice'];
        }

        if ($query['minStock'] && $query['maxStock']) {
            $filter['stock BETWEEN ? AND ?'] = [$query['minStock'], $query['maxStock']];
        } elseif ($query['minStock']) {
            $filter['stock >= ?'] = $query['minStock'];
        } elseif ($query['maxStock']) {
            $filter['stock <= ?'] = $query['maxStock'];
        }

        try {
            $this->data = $this->productRepo->getList($query['limit'], $query['offset'], $filter);
            return true;
        } catch (ProductException $e) {
            $this->setError($e->getCode(), $e->getMessage());
        }

        return false;
    }
}
