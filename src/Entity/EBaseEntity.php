<?php

namespace App\Entity;

use JsonSerializable;

abstract class EBaseEntity implements JsonSerializable
{
    protected array $extra = [];

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

    protected function rememberExtra(array $source, array $knownKeys): void
    {
        $known = [];

        foreach ($knownKeys as $key) {
            $known[$key] = true;
            $known[self::snakeToCamel($key)] = true;
        }

        $this->extra = array_diff_key($source, $known);
    }

    protected function withExtra(array $data): array
    {
        return array_merge($this->extra, $data);
    }

    private static function snakeToCamel(string $key): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }

    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
