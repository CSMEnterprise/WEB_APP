<?php

namespace App\Middleware;

function requireAuth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?route=login');
        exit;
    }
}

function isLoggedIn(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return !empty($_SESSION['user_id']);
}

function currentUserId(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return (int) ($_SESSION['user_id'] ?? 0);
}
