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
     * Insert new product
     *
     * @param int $id Product ID
     *
     * @return ProductEntity Product data
     * @throws ProductException If product not found
     */
    public function getProductById(int $id): ProductEntity
    {
        $row = $this->db->table(self::TABLE_NAME)
            ->where('id', $id)
            ->fetch();

        if (!$row) {
            throw new ProductException("Product ID: $id not found.", Response::S404_NotFound);
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
        if ($product->id !== null) {
            $product->id = null;
        }

        $row = $this->db->table(self::TABLE_NAME)
            ->insert($product->toDatabaseRow());

        $product->id = (int) $row->getPrimary();

        return $product;
    }

    /**
     * Product update
     *
     * @param ProductEntity $product Product data
     *
     * @return int Number of affected rows
     * @throws ProductException If product entity does not have set ID
     */
    public function update(ProductEntity $product): int
    {
        if ($product->id === null) {
            throw new ProductException('Product Entity must have an ID to be updated.', Response::S400_BadRequest);
        }

        return $this->db->table(self::TABLE_NAME)
            ->where('id', $product->id)
            ->update($product->toDatabaseRow());
    }

    /**
     * Check if product exists
     *
     * @param int $id Product ID
     * @param bool $includeDeleted Whether to include products marked as deleted (default: false)
     *
     * @return bool True if product exist, otherwise false
     */
    public function exists(int $id, bool $includeDeleted = false): bool
    {
        $query = $this->db->table(self::TABLE_NAME)
            ->select('id')
            ->where('id', $id);

        if (!$includeDeleted) {
            $query->where('deleted', 0);
        }

        return $query->fetch() !== null;
    }

    /**
     * Product delete
     *
     * @param int $id Product ID
     *
     * @return int Number of affected rows
     * @throws ProductException If product not found
     *
     * @todo Delete also rows from the price_history table?
     */
    public function delete(int $id): int
    {
        if (!$this->exists($id)) {
            throw new ProductException("Product ID: $id not found.", Response::S404_NotFound);
        }

        return $this->db->table(self::TABLE_NAME)
            ->where('id', $id)
            ->delete();
    }

    /**
     * Set product 'deleted' status (eg. 'soft delete')
     *
     * @param int $id Product ID
     * @param bool $deleted Deleted flag
     *
     * @return int Number of affected rows
     * @throws ProductException If product not found
     */
    public function setDeleted(int $id, bool $deleted): int
    {
        if (!$this->exists($id)) {
            throw new ProductException("Product ID: $id not found.", Response::S404_NotFound);
        }

        return $this->db->table(self::TABLE_NAME)
            ->where('id', $id)
            ->update(['deleted' => (int) $deleted]);
    }
}

/*
CREATE TABLE `product` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `price` FLOAT NOT NULL,
    `stock` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted` INT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
