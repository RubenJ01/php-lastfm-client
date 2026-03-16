<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Concerns;

/**
 * Provides a human-readable __toString() for DTOs using reflection.
 *
 * Top-level output is multi-line:
 *   UserDto {
 *     name: "RJ"
 *     playcount: 150316
 *   }
 *
 * Nested objects and arrays are rendered inline for compactness.
 */
trait HasToString
{
    public function __toString(): string
    {
        $class = (new \ReflectionClass($this))->getShortName();
        $lines = [];

        foreach ((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $lines[] = '  ' . $prop->getName() . ': ' . self::formatValue($prop->getValue($this));
        }

        return $class . " {\n" . implode("\n", $lines) . "\n}";
    }

    private static function formatValue(mixed $value): string
    {
        /** @phpstan-ignore match.unhandled */
        return match (true) {
            $value === null => 'null',
            is_string($value) => '"' . $value . '"',
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string) $value,
            $value instanceof \DateTimeInterface => $value->format(\DateTimeInterface::ATOM),
            is_object($value) => self::formatObject($value),
            is_array($value) => self::formatArray($value),
        };
    }

    private static function formatObject(object $value): string
    {
        $class = (new \ReflectionClass($value))->getShortName();
        $parts = [];

        foreach ((new \ReflectionClass($value))->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $parts[] = $prop->getName() . ': ' . self::formatValue($prop->getValue($value));
        }

        return $class . ' { ' . implode(', ', $parts) . ' }';
    }

    /**
     * @param array<mixed> $value
     */
    private static function formatArray(array $value): string
    {
        return '[' . implode(', ', array_map(self::formatValue(...), $value)) . ']';
    }
}
