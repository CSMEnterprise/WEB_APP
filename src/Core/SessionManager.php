<?php

namespace App\Core;

use RuntimeException;

/**
 * Gestisce configurazione, rinnovo e distruzione delle sessioni PHP.
 */
final class SessionManager
{
    private const DEFAULT_IDLE_TIMEOUT = 1800;

    private function __construct()
    {
    }

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::refreshActivity();
            return;
        }

        if (headers_sent($file, $line)) {
            throw new RuntimeException("Impossibile avviare la sessione: output gia inviato da {$file}:{$line}.");
        }

        self::configure();

        if (!session_start()) {
            throw new RuntimeException('Impossibile avviare la sessione PHP.');
        }

        if (self::isExpired()) {
            self::destroy();
            self::start();
            $_SESSION['session_expired'] = true;
            return;
        }

        self::refreshActivity();
    }

    /**
     * Elimina il vecchio ID dopo il login e prepara una sessione autenticata pulita.
     */
    public static function regenerateForAuthentication(): void
    {
        self::start();

        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Impossibile rigenerare la sessione dopo il login.');
        }

        $_SESSION = [];
        $_SESSION['authenticated_at'] = time();
        $_SESSION['last_activity'] = time();
    }

    /**
     * Cancella dati server-side e cookie di sessione nel browser.
     */
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        if ((bool) ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) ($params['secure'] ?? false),
                'httponly' => (bool) ($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }

    public static function idleTimeout(): int
    {
        $configured = getenv('SESSION_IDLE_TIMEOUT');

        if ($configured === false || !ctype_digit((string) $configured)) {
            return self::DEFAULT_IDLE_TIMEOUT;
        }

        return (int) $configured;
    }

    private static function configure(): void
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => self::isHttpsRequest() || self::envFlag('SESSION_COOKIE_SECURE'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private static function isExpired(): bool
    {
        if (empty($_SESSION['user_id'])) {
            return false;
        }

        $timeout = self::idleTimeout();
        $lastActivity = (int) ($_SESSION['last_activity'] ?? 0);

        return $timeout > 0
            && $lastActivity > 0
            && (time() - $lastActivity) > $timeout;
    }

    private static function refreshActivity(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }
    }

    private static function isHttpsRequest(): bool
    {
        return (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
    }

    private static function envFlag(string $name): bool
    {
        $value = getenv($name);

        return $value !== false
            && in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }
}
