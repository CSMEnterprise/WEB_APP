<?php

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    private const FIELD_NAME = 'csrf_token';

    public static function fieldName(): string
    {
        return self::FIELD_NAME;
    }

    public static function token(): string
    {
        self::ensureSession();

        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate(mixed $token): bool
    {
        self::ensureSession();

        $stored = $_SESSION[self::SESSION_KEY] ?? '';

        return is_string($token)
            && is_string($stored)
            && $token !== ''
            && $stored !== ''
            && hash_equals($stored, $token);
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
