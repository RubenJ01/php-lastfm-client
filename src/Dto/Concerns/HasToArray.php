<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Concerns;

/**
 * Provides a recursive toArray() method for DTOs using reflection.
 *
 * Converts all public properties to an associative array, recursively
 * converting nested objects and arrays of objects.
 */
trait HasToArray
{
    /**
     * Convert this DTO to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];

        foreach ((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $result[$prop->getName()] = self::convertValue($prop->getValue($this));
        }

        return $result;
    }

    private static function convertValue(mixed $value): mixed
    {
        /** @phpstan-ignore match.unhandled */
        return match (true) {
            $value === null, is_string($value), is_int($value), is_float($value), is_bool($value) => $value,
            $value instanceof \DateTimeInterface => $value->format(\DateTimeInterface::ATOM),
            is_object($value) => self::convertObject($value),
            is_array($value) => self::convertArray($value),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function convertObject(object $value): array
    {
        $result = [];

        foreach ((new \ReflectionClass($value))->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $result[$prop->getName()] = self::convertValue($prop->getValue($value));
        }

        return $result;
    }

    /**
     * @param array<mixed> $value
     * @return array<mixed>
     */
    private static function convertArray(array $value): array
    {
        return array_map(self::convertValue(...), $value);
    }
}
