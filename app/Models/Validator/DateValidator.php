<?php

declare(strict_types=1);

namespace App\Models\Validator;

use DateTime;
use DateTimeImmutable;
use Nette\InvalidArgumentException;

final class DateValidator
{
    /**
     * Validates and converts a date string to a DateTime object.
     *
     * @param mixed $value The value to validate and convert.
     * @param string $fieldName The name of the field for error messages.
     * @return DateTime|null Returns a DateTime object or null if the value is empty.
     * @throws InvalidArgumentException If the value is not a valid date string.
     */
    public static function getValidDateTime(mixed $value, string $fieldName = 'datetime'): ?DateTime
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \Nette\Utils\DateTime) {
            $value = (string) $value;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException("Invalid type for {$fieldName}: expected string, got " . gettype($value));
        }

        $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        if (!$dt) {
            throw new InvalidArgumentException("Invalid format for {$fieldName}: '{$value}' (expected Y-m-d H:i:s)");
        }

        return DateTime::createFromImmutable($dt);
    }
}
