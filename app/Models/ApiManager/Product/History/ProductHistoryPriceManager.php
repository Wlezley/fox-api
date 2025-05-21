<?php

declare(strict_types=1);

namespace App\Models\ApiManager;

use App\Models\ApiException\ProductHistoryException;
use App\Models\Repository\ProductHistoryRepository;
use Nette\Http\Request;
use Nette\Http\Response;

/**
 * API manager for handling /product/history/price/ related REST operations.
 *
 * Supports only the HTTP GET method.
 */
final class ProductHistoryPriceManager extends ApiManager
{
    protected array $allowedMethods = [
        Request::Get
    ];

    /**
     * @param Request $httpRequest Current HTTP request
     * @param Response $httpResponse Current HTTP response
     * @param ProductHistoryRepository $productHistoryRepo Product history repository instance
     */
    public function __construct(
        public Request $httpRequest,
        public Response $httpResponse,
        public ProductHistoryRepository $productHistoryRepo
    ) {}

    /**
     * Handles HTTP GET request to retrieve a product price history by product ID.
     *
     * Supports optional pagination via `limit` and `offset` query parameters.
     *
     * @return bool True on success, false on failure (e.g. missing or invalid ID)
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
                $limit = $this->httpRequest->getQuery('limit') ?? 50;
                $offset = $this->httpRequest->getQuery('offset') ?? 0;

                $data = $this->productHistoryRepo
                    ->getHistoryByProductId((int) $id, (int) $limit, (int) $offset);

                $proce_old = null;
                $priceData = [];
                foreach ($data as $item) {
                    $price_changed = false;
                    if ($item['price'] != $proce_old) {
                        $price_changed = true;
                    }

                    $priceData[] = [
                        'guid'=> $item['id'],
                        'price' => $item['price'],
                        'price_old' => $proce_old,
                        'price_changed' => $price_changed,
                        'changed_at' => $item['changed_at']
                    ];

                    $proce_old = $item['price'];
                }
                $this->data = $priceData;
                return true;
            } catch (ProductHistoryException $e) {
                $this->setError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }
}
