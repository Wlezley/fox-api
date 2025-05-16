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
     * Create an instance from the database record
     *
     * @param array<string,mixed> $row Fetched row from the database
     * @return ProductEntity
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
     * Returns prepared data for database INSERT/UPDATE
     *
     * @return array<string,mixed> Prepared data for database INSERT/UPDATE
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
     * Returns prepared data for JSON response
     *
     * @return array<string,mixed> Prepared data for JSON response
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
     * Create an instance from the database record
     *
     * @param array<string,mixed> $data Data to update
     * @return ProductEntity
     */
    public function preapreForUpdate(array $data): self
    {
        $this->name = (string) ($data['name'] ?? $this->name);
        $this->price = (float) ($data['price'] ?? $this->price);
        $this->stock = (int) ($data['stock'] ?? $this->stock);
        $this->deleted = (bool) ($data['deleted'] ?? $this->deleted);

        return $this;
    }
}
