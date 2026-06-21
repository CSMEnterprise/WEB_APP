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

        $stored = SessionManager::get(self::SESSION_KEY);
        if (empty($stored) || !is_string($stored)) {
            $stored = bin2hex(random_bytes(32));
            SessionManager::set(self::SESSION_KEY, $stored);
        }

        return $stored;
    }

    public static function validate(mixed $token): bool
    {
        self::ensureSession();

        $stored = SessionManager::get(self::SESSION_KEY, '');

        return is_string($token)
            && is_string($stored)
            && $token !== ''
            && $stored !== ''
            && hash_equals($stored, $token);
    }

    private static function ensureSession(): void
    {
        SessionManager::start();
    }
}
