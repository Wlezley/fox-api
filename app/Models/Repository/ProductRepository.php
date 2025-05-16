<?php

declare(strict_types=1);

namespace App\Models\Repository;

use App\Models\Entity\ProductEntity;
use App\Models\Exception\ProductException;
use Nette\Http\Response;

final class ProductRepository extends BaseRepository
{
    public const TABLE_NAME = 'product';

    /**
     * Get product by ID
     *
     * @param int $productId Product ID
     *
     * @return ProductEntity Product data
     * @throws ProductException If product not found
     */
    public function getProductById(int $productId): ProductEntity
    {
        $row = $this->db->table(self::TABLE_NAME)
            ->where('id', $productId)
            ->fetch();

        if (!$row) {
            throw new ProductException("Product ID: $productId not found.", Response::S404_NotFound);
        }

        return ProductEntity::fromDatabaseRow($row->toArray());
    }

    /**
     * Insert new product
     *
     * @param ProductEntity $product Product data
     *
     * @return ProductEntity Product data with ID of new product
     */
    public function insert(ProductEntity $product): ProductEntity
    {
        $data = $product->toDatabaseRow();
        unset($data['id'], $data['created_at'], $data['updated_at']);

        $row = $this->db->table(self::TABLE_NAME)
            ->insert($data);

        $id = (int) $row->getPrimary();
        $newProduct = $this->getProductById($id);

        (new ProductHistoryRepository($this->db))
            ->createFromProduct($newProduct);

        return $newProduct;
    }

    /**
     * Product update
     *
     * @param ProductEntity $product Product data
     *
     * @return ProductEntity Data of updated product
     * @throws ProductException If product entity does not have set ID
     */
    public function update(ProductEntity $product): ProductEntity
    {
        if ($product->id === null) {
            throw new ProductException('Product Entity must have an ID to be updated.', Response::S400_BadRequest);
        }

        $data = $product->toDatabaseRow();
        unset($data['id'], $data['created_at'], $data['updated_at']);

        $affectedRows = $this->db->table(self::TABLE_NAME)
            ->where('id', $product->id)
            ->update($data);

        $updatedProduct = $this->getProductById($product->id);

        if ($affectedRows > 0) {
            (new ProductHistoryRepository($this->db))
                ->createFromProduct($updatedProduct);
        }

        return $updatedProduct;
    }

    /**
     * Check if product exists
     *
     * @param int $productId Product ID
     * @param bool $includeDeleted Whether to include products marked as deleted (default: false)
     *
     * @return bool True if product exist, otherwise false
     */
    public function exists(int $productId, bool $includeDeleted = false): bool
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->select('id')
            ->where('id', $productId);

        if (!$includeDeleted) {
            $query->where('deleted', 0);
        }

        return $query->fetch() !== null;
    }

    /**
     * Product delete
     *
     * @param int $productId Product ID
     *
     * @return int Number of affected rows
     * @throws ProductException If product not found
     *
     * @todo Delete also rows from the price_history table?
     */
    public function delete(int $productId): int
    {
        if (!$this->exists($productId)) {
            throw new ProductException("Product ID: $productId not found.", Response::S404_NotFound);
        }

        return $this->db->table(self::TABLE_NAME)
            ->where('id', $productId)
            ->delete();
    }

    /**
     * Set product 'deleted' status (eg. 'soft delete')
     *
     * @param int $productId Product ID
     * @param bool $deleted Deleted flag
     *
     * @return int Number of affected rows
     * @throws ProductException If product not found
     */
    public function setDeleted(int $productId, bool $deleted): int
    {
        if (!$this->exists($productId)) {
            throw new ProductException("Product ID: $productId not found.", Response::S404_NotFound);
        }

        return $this->db->table(self::TABLE_NAME)
            ->where('id', $productId)
            ->update(['deleted' => (int) $deleted]);
    }

    /**
     * Retrieves a list of products with optional filter and pagination
     *
     * @param int $limit Number of results to return (default: 50)
     * @param int $offset Offset for pagination (default: 0)
     * @param array<string,string> $filter Optional filter query parts for WHERE clause (default: empty)
     *
     * @return array<string,mixed>[] List of products
     * @throws ProductException If no products are found
     */
    public function getList(int $limit = 50, int $offset = 0, array $filter = []): array
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->limit($limit, $offset)
            ->whereOr($filter)
            ->order('id ASC');

        $data = $query->fetchAll();

        $productList = [];
        foreach ($data as $item) {
            $productList[] = ProductEntity::fromDatabaseRow($item->toArray())->toArray();
        }

        if (empty($productList)) {
            throw new ProductException("No products are found.", Response::S404_NotFound);
        }

        return $productList;
    }

    /**
     * Get count of products by optional filter parameter
     *
     * @param array<string,string> $filter Optional filter query parts for WHERE clause (default: empty)
     *
     * @return int Count of products
     */
    public function getCount(array $filter = []): int
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->whereOr($filter);

        return $query->count('*');
    }
}
