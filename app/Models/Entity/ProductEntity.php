<?php

declare(strict_types=1);

namespace App\Models\Entity;

use DateTime;

final class ProductEntity
{
    public ?int $id = null;
    public string $name = '';
    public float $price = 0;
    public int $stock = 0;
    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
    public bool $deleted = false;

    /**
     * Creates a new ProductEntity instance from a database row.
     *
     * @param array<string,mixed> $row Fetched row from the database.
     * @return ProductEntity
     *
     * @todo Throw ProductException If DateTime parsing is later added and fails (?)
     */
    public static function fromDatabaseRow(array $row): self
    {
        $product = new self();
        $product->id = isset($row['id']) ? (int) $row['id'] : null;
        $product->name = (string) ($row['name'] ?? '');
        $product->price = (float) ($row['price'] ?? 0);
        $product->stock = (int) ($row['stock'] ?? 0);
        $product->created_at = $row['created_at'] ?? null; // TODO: Validate DateTime input format
        $product->updated_at = $row['updated_at'] ?? null; // TODO: Validate DateTime input format
        $product->deleted = (bool) ($row['deleted'] ?? false);

        return $product;
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
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
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
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at ? $this->created_at->format('c') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('c') : null,
            'deleted' => $this->deleted,
        ];
    }

    /**
     * Updates entity fields based on input data and returns the updated entity.
     *
     * @param array<string,mixed> $data Partial data to update the entity.
     * @return ProductEntity Updated entity instance.
     */
    public function prepareForUpdate(array $data): self
    {
        $this->name = (string) ($data['name'] ?? $this->name);
        $this->price = (float) ($data['price'] ?? $this->price);
        $this->stock = (int) ($data['stock'] ?? $this->stock);
        $this->deleted = (bool) ($data['deleted'] ?? $this->deleted);

        return $this;
    }
}
