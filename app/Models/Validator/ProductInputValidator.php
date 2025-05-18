<?php

declare(strict_types=1);

namespace App\Models\Validator;

use Nette\InvalidArgumentException;

final class ProductInputValidator
{
    /**
     * Validates and normalizes product input data.
     *
     * This method checks if the required fields are present and of the correct type.
     * It also normalizes the data by trimming strings and converting numeric values to their appropriate types.
     *
     * @param array<string, mixed> $data Input data to validate
     *
     * The expected structure of the input data is:
     * - 'name': string (required)
     * - 'price': float (non-negative)
     * - 'stock': int (non-negative)
     *
     * @return array<string, mixed> Validated and normalized data
     * @throws InvalidArgumentException If any of the required fields are missing or invalid
     */
    public static function validate(array $data): array
    {
        if (!isset($data['name']) || !is_string($data['name']) || trim($data['name']) === '') {
            throw new InvalidArgumentException("Field 'name' is required and must be a non-empty string.");
        }

        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] < 0)) {
            throw new InvalidArgumentException("Field 'price' must be a non-negative number (with decimal point).");
        }

        if (isset($data['stock']) && (!is_int($data['stock']) || $data['stock'] < 0)) {
            throw new InvalidArgumentException("Field 'stock' must be a non-negative integer.");
        }

        return [
            'name' => trim($data['name']),
            'price' => (float) ($data['price'] ?? 0),
            'stock' => (int) ($data['stock'] ?? 0),
        ];
    }
}
