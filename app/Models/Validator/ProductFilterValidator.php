<?php

declare(strict_types=1);

namespace App\Models\Validator;

use Nette\Http\Response;

class ProductFilterValidator
{
    /**
     * Validates query parameters and returns a normalized filter array.
     *
     * @param mixed $query Query parameters (typically from $httpRequest->getQuery())
     * @return array{
     *     name?: ?string,
     *     minPrice?: ?float,
     *     maxPrice?: ?float,
     *     minStock?: ?int,
     *     maxStock?: ?int,
     *     limit: int,
     *     offset: int
     * }
     *
     * @throws \Nette\InvalidArgumentException If any parameter has an invalid format or type.
     */
    public static function getValidatedFilters(mixed $query): array
    {
        if (empty($query) || !is_array($query)) {
            return [
                'limit' => 50,
                'offset' => 0
            ];
        }

        $result = [];

        // Name
        $result['name'] = $query['name'] ?? null;
        if ($result['name'] !== null && !is_string($result['name'])) {
            throw new \Nette\InvalidArgumentException("Parameter 'name' must be a string", Response::S400_BadRequest);
        }

        // Price
        $result['minPrice'] = self::validateFloatParam($query, 'minPrice');
        $result['maxPrice'] = self::validateFloatParam($query, 'maxPrice');

        // Stock
        $result['minStock'] = self::validateIntParam($query, 'minStock');
        $result['maxStock'] = self::validateIntParam($query, 'maxStock');

        // Pagination
        $result['limit'] = self::validateIntParam($query, 'limit', 50, 1, 100);
        $result['offset'] = self::validateIntParam($query, 'offset', 0, 0);

        return $result;
    }

    /**
     * Validates that the given query parameter is a float-compatible numeric value.
     *
     * @param array<string,mixed> $query Query array to validate from
     * @param string $key Parameter name to check
     * @return float|null Parsed float value or null if the parameter is not set
     *
     * @throws \Nette\InvalidArgumentException If the parameter exists but is not numeric
     */
    private static function validateFloatParam(array $query, string $key): ?float
    {
        if (!isset($query[$key])) {
            return null;
        }

        if (!is_numeric($query[$key])) {
            throw new \Nette\InvalidArgumentException("Parameter '$key' must be numeric", Response::S400_BadRequest);
        }

        return (float) $query[$key];
    }

    /**
     * Validates that the given query parameter is an integer, with optional default and bounds.
     *
     * @param array<string,mixed> $query Query array to validate from
     * @param string $key Parameter name to check
     * @param int|null $default Default value to return if the parameter is not set
     * @param int|null $min Minimum allowed value (if provided)
     * @param int|null $max Maximum allowed value (if provided)
     * @return int|null Parsed and bounded integer value, or default if not set
     *
     * @throws \Nette\InvalidArgumentException If the parameter exists but is not numeric
     */
    private static function validateIntParam(
        array $query,
        string $key,
        ?int $default = null,
        ?int $min = null,
        ?int $max = null
    ): ?int
    {
        if (!isset($query[$key])) {
            return $default;
        }

        if (!is_numeric($query[$key])) {
            throw new \Nette\InvalidArgumentException("Parameter '$key' must be an integer", Response::S400_BadRequest);
        }

        $value = (int) $query[$key];

        if ($min !== null && $value < $min) {
            $value = $min;
        }

        if ($max !== null && $value > $max) {
            $value = $max;
        }

        return $value;
    }
}
