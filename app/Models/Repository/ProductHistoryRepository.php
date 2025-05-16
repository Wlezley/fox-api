<?php

declare(strict_types=1);

namespace App\Models\Repository;

use App\Models\Entity\ProductEntity;
use App\Models\Entity\ProductHistoryEntity;
use App\Models\Exception\ProductHistoryException;
use Nette\Http\Response;

final class ProductHistoryRepository extends BaseRepository
{
    public const TABLE_NAME = 'product_history';

    /**
     * Create new product history record
     *
     * @param ProductEntity $product Product data
     *
     * @return int Product history ID
     */
    public function createFromProduct(ProductEntity $product): int
    {
        $productHistory = ProductHistoryEntity::fromProductEntity($product);

        $row = $this->db->table(self::TABLE_NAME)
            ->insert($productHistory->toDatabaseRow());

        return (int) $row->getPrimary();
    }

    /**
     * Check if product have history records
     *
     * @param int $productId Product ID
     *
     * @return bool True if product exist, otherwise false
     */
    public function exists(int $productId): bool
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->select('id')
            ->where('product_id', $productId)
            ->limit(1);

        return $query->fetch() !== null;
    }

    /**
     * Retrieves a list of product history records with optional pagination
     *
     * @param int $productId Product ID
     * @param int $limit Number of results to return (min: 1, max: 100, default: 50)
     * @param int $offset Offset for pagination (default: 0)
     *
     * @return array<string,mixed>[] List of product history records
     *
     * @throws ProductHistoryException If limit value is out of range
     * @throws ProductHistoryException If offset is greater than the number of history records
     * @throws ProductHistoryException If product not found or doesn't have any history records yet
     */
    public function getHistoryByProductId(int $productId, int $limit = 50, int $offset = 0): array
    {
        if (filter_var($limit, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]) === false) {
            throw new ProductHistoryException(
                "The limit is not in the allowed range; an integer between 1 and 100 is expected.",
                Response::S400_BadRequest
            );
        }

        $data = $this->db->table(self::TABLE_NAME)
            ->where('product_id', $productId)
            ->limit($limit, $offset)
            ->order('changed_at ASC')
            ->fetchAll();

        $productHistoryList = [];
        foreach ($data as $item) {
            $productHistoryList[] = ProductHistoryEntity::fromDatabaseRow($item->toArray())->toArray();
        }

        if (empty($productHistoryList)) {
            $count = $this->getCount($productId);

            if ($count != 0 && $count <= ($offset * $limit)) {
                throw new ProductHistoryException(
                    "The offset is greater than the number of records.",
                    Response::S400_BadRequest
                );
            }

            throw new ProductHistoryException(
                "History data for product ID: $productId not found.",
                Response::S404_NotFound
            );
        }

        return $productHistoryList;
    }

    /**
     * Get count of product history records by product ID
     *
     * @param int $productId Product ID
     *
     * @return int Count of product history records
     */
    public function getCount(int $productId): int
    {
        return $this->db->table(self::TABLE_NAME)
            ->where('product_id', $productId)
            ->count('*');
    }
}
