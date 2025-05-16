<?php

declare(strict_types=1);

namespace App\Models\Entity;

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
     * Create an instance from the database record
     *
     * @param array<string,mixed> $row Fetched row from the database
     * @return ProductHistoryEntity
     */
    public static function fromDatabaseRow(array $row): self
    {
        $product = new self();
        $product->id = isset($row['id']) ? (int) $row['id'] : null;
        $product->product_id = (int) ($row['product_id'] ?? 0);
        $product->name = (string) ($row['name'] ?? '');
        $product->price = (float) ($row['price'] ?? 0);
        $product->stock = (int) ($row['stock'] ?? 0);
        // $product->changed_at = $row['changed_at'] ?? new DateTime('now');
        $product->changed_at = $row['changed_at'] ?? null; // TODO: Validate DateTime input format

        return $product;
    }

    /**
     * Create an instance from the product entity
     *
     * @param ProductEntity $product Product data
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
     * Returns prepared data for database INSERT/UPDATE
     *
     * @return array<string,mixed> Prepared data for database INSERT/UPDATE
     */
    public function toDatabaseRow(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            // 'changed_at' => $this->changed_at,
        ];
    }

    /**
     * Returns prepared data for JSON response
     *
     * @return array<string,mixed> Prepared data for JSON response
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
