<?php

declare(strict_types=1);

namespace App\Models\Entity;

use App\Models\Validator\DateValidator;
use DateTime;

final class ProductHistoryEntity
{
    public ?int $id = null;
    public int $product_id = 0;
    public string $name = '';
    public float $price = 0;
    public int $stock = 0;
    public ?DateTime $changed_at = null;

    /**
     * Creates a new ProductHistoryEntity instance from a database row.
     *
     * @param array<string,mixed> $row Fetched row from the database.
     * @return ProductHistoryEntity New ProductHistoryEntity instance.
     */
    public static function fromDatabaseRow(array $row): self
    {
        $product = new self();
        $product->id = isset($row['id']) ? (int) $row['id'] : null;
        $product->product_id = (int) ($row['product_id'] ?? 0);
        $product->name = (string) ($row['name'] ?? '');
        $product->price = (float) ($row['price'] ?? 0);
        $product->stock = (int) ($row['stock'] ?? 0);
        $product->changed_at = isset($row['changed_at'])
            ? DateValidator::getValidDateTime($row['changed_at'], 'changed_at')
            : null;

        return $product;
    }

    /**
     * Creates a ProductHistoryEntity instance from a ProductEntity object.
     *
     * @param ProductEntity $product Product data used to initialize the history entity.
     * @return ProductHistoryEntity
     */
    public static function fromProductEntity(ProductEntity $product): self
    {
        $productHistory = new self();
        $productHistory->product_id = $product->id;
        $productHistory->name = $product->name;
        $productHistory->price = $product->price;
        $productHistory->stock = $product->stock;
        $productHistory->changed_at = $product->updated_at;

        return $productHistory;
    }

    /**
     * Prepares the entity data for a database INSERT or UPDATE.
     *
     * @return array<string,mixed> Prepared associative array for database operations.
     */
    public function toDatabaseRow(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }

    /**
     * Converts the entity to an array suitable for JSON serialization.
     *
     * @return array<string,mixed> Prepared associative array for API response.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'changed_at' => $this->changed_at ? $this->changed_at->format('c') : null,
        ];
    }
}
