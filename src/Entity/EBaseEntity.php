<?php

namespace App\Entity;

use JsonSerializable;

/**
 * Classe base astratta per tutte le entity del progetto.
 *
 * Fornisce:
 * - helper per leggere array con chiavi snake_case o camelCase
 * - conversioni tipo-sicure (int, float, bool) compatibili con i valori restituiti da PDO
 * - gestione dei campi "extra" non mappati, preservati in toArray() / jsonSerialize()
 */
abstract class EBaseEntity implements JsonSerializable
{
    /** Campi aggiuntivi non dichiarati nell'entity, mantenuti per non perderli nel round-trip DB→oggetto→DB. */
    protected array $extra = [];

    /**
     * Legge un valore da un array provando prima la chiave snake_case poi quella camelCase.
     * Restituisce $default se nessuna delle due esiste.
     */
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

    /** Converte un valore in int, restituendo null se vuoto o null. */
    protected static function intOrNull(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    /** Converte un valore in float, restituendo null se vuoto o null. */
    protected static function floatOrNull(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    /**
     * Converte un valore proveniente dal DB in bool.
     * Gestisce interi (0/1), stringhe ('true','yes','on','1') e bool nativi.
     */
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

    /** Converte un bool PHP nel formato intero 0/1 usato nelle colonne TINYINT del DB. */
    protected static function boolToDb(bool $value): int
    {
        return $value ? 1 : 0;
    }

    /**
     * Salva in $this->extra tutti i campi dell'array sorgente che non fanno parte
     * delle chiavi dichiarate dall'entity (né in snake_case né in camelCase).
     * Chiamare alla fine di fromArray() per non perdere colonne aggiuntive (es. JOIN).
     */
    protected function rememberExtra(array $source, array $knownKeys): void
    {
        $known = [];

        foreach ($knownKeys as $key) {
            $known[$key] = true;
            $known[self::snakeToCamel($key)] = true;
        }

        $this->extra = array_diff_key($source, $known);
    }

    /** Unisce i campi extra ai dati dell'entity prima di restituirli. */
    protected function withExtra(array $data): array
    {
        return array_merge($this->extra, $data);
    }

    /** Converte una chiave snake_case in camelCase. */
    private static function snakeToCamel(string $key): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }

    /** Restituisce l'entity come array associativo con chiavi snake_case. */
    abstract public function toArray(): array;

    /** Implementa JsonSerializable delegando a toArray(). */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
