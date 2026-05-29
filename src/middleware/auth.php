<?php

namespace App\Middleware;

/**
 * Verifica che l'utente sia autenticato.
 * Se la sessione non contiene `user_id`, reindirizza al login e termina.
 * Usare come primo controllo in tutte le route protette.
 */
function requireAuth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        header('Location: /auth/login');
        exit;
    }
}

/**
 * Controlla se esiste una sessione utente attiva senza bloccare l'esecuzione.
 * Utile nei template per mostrare/nascondere elementi condizionalmente.
 */
function isLoggedIn(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return !empty($_SESSION['user_id']);
}

/**
 * Restituisce l'ID dell'utente loggato (0 se non autenticato).
 * Comodo per passare l'ID ai DAO senza accedere direttamente a $_SESSION.
 */
function currentUserId(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return (int) ($_SESSION['user_id'] ?? 0);
}
