<?php

abstract class EBaseEntity implements JsonSerializable
{
    protected static function read(array $data, string $snakeKey, ?string $camelKey = null, mixed $default = null): mixed
    {
        if (array_key_exists($snakeKey, $data)) {
            return $data[$snakeKey];
        }

        if ($camelKey !== null && array_key_exists($camelKey, $data)) {
            return $data[$camelKey];
        }

        return $default;
    }

    protected static function intOrNull(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    protected static function floatOrNull(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    protected static function boolFromDb(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    protected static function boolToDb(bool $value): int
    {
        return $value ? 1 : 0;
    }

    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
