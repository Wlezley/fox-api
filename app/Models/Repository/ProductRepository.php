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
     * Retrieves a product by its ID.
     *
     * @param int $productId Product ID.
     * @return ProductEntity Product data.
     *
     * @throws ProductException If the product is not found.
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
     * Inserts a new product into the database and returns the complete entity.
     *
     * Automatically creates a product history entry.
     *
     * @param ProductEntity $product Product data.
     * @return ProductEntity Product data with the assigned ID.
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
     * Updates an existing product in the database and returns the updated entity.
     *
     * Automatically creates a product history entry if data was modified.
     *
     * @param ProductEntity $product Product entity to update.
     * @return ProductEntity Updated product entity.
     *
     * @throws ProductException If the product ID is not set.
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
     * Checks whether a product exists in the database.
     *
     * @param int $productId Product ID.
     * @param bool $includeDeleted Whether to include soft-deleted products (default: false).
     * @return bool True if the product exists, otherwise false.
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
     * Permanently deletes a product by its ID.
     *
     * @param int $productId Product ID.
     * @return int Number of affected rows.
     *
     * @throws ProductException If the product does not exist.
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
     * Marks a product as deleted or restores it (soft delete).
     *
     * This does not remove the product from the database but marks it as deleted.
     *
     * @param int $productId Product ID.
     * @param bool $deleted Whether the product should be marked as deleted.
     * @return int Number of affected rows.
     *
     * @throws ProductException If the product does not exist.
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
     * Retrieves a list of products with optional filtering and pagination.
     *
     * @param int $limit Maximum number of products to return (default: 50).
     * @param int $offset Offset for pagination (default: 0).
     * @param array<string,string> $filter Optional filter conditions for WHERE clause.
     * @return array<array<string,mixed>> List of products formatted as associative arrays.
     *
     * @throws ProductException If the limit or offset is out of range, or if no products are found.
     */
    public function getList(int $limit = 50, int $offset = 0, array $filter = []): array
    {

        if (filter_var($limit, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]) === false) {
            throw new ProductException(
                "The limit is not in the allowed range; an integer between 1 and 100 is expected.",
                Response::S400_BadRequest
            );
        }
        if (filter_var($offset, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            throw new ProductException(
                "The offset is not in the allowed range; a non-negative integer is expected.",
                Response::S400_BadRequest
            );
        }

        $query = $this->db->table(self::TABLE_NAME)
            ->limit($limit, $offset)
            ->where($filter)
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
     * Returns the total number of products, optionally filtered.
     *
     * @param array<string,string> $filter Optional filter conditions for WHERE clause.
     * @return int Count of matching products.
     */
    public function getCount(array $filter = []): int
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->whereOr($filter);

        return $query->count('*');
    }
}
