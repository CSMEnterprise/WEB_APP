<?php

namespace App\Core;

/**
 * Incapsula l'accesso a $_GET, $_POST, $_SERVER e $_FILES.
 * Nessun controller o middleware deve leggere le superglobali direttamente.
 */
final class Request
{
    private function __construct()
    {
    }

    public static function method(): string
    {
        return strtoupper((string) (self::server('REQUEST_METHOD') ?? 'GET'));
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function get(?string $key = null, mixed $default = null): mixed
    {
        return self::read($_GET, $key, $default);
    }

    public static function post(?string $key = null, mixed $default = null): mixed
    {
        return self::read($_POST, $key, $default);
    }

    public static function server(?string $key = null, mixed $default = null): mixed
    {
        return self::read($_SERVER, $key, $default);
    }

    /**
     * Restituisce la voce $_FILES grezza (per upload multipli) o, se $key e' una
     * singola voce di upload, un'istanza di UploadedFile.
     */
    public static function files(?string $key = null): mixed
    {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? [];
    }

    /**
     * Restituisce un singolo file caricato (campo non multiplo) come UploadedFile.
     */
    public static function file(string $key): UploadedFile
    {
        return UploadedFile::fromArray($_FILES[$key] ?? []);
    }

    public static function postInt(string $key, int $default = 0): int
    {
        return (int) self::post($key, $default);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    private static function read(array $source, ?string $key, mixed $default): mixed
    {
        if ($key === null) {
            return $source;
        }

        return $source[$key] ?? $default;
    }
}
