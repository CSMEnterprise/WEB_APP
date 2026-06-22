<?php

namespace App\Middleware;

use App\Core\SessionManager;

/**
 * Verifica che l'utente sia autenticato.
 * Se la sessione non contiene `user_id`, reindirizza al login e termina.
 * Usare come primo controllo in tutte le route protette.
 */
function requireAuth(): void
{
    SessionManager::start();

    if (!SessionManager::has('user_id')) {
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
    SessionManager::start();

    return SessionManager::has('user_id');
}

/**
 * Restituisce l'ID dell'utente loggato (0 se non autenticato).
 * Comodo per passare l'ID ai DAO senza accedere direttamente a $_SESSION.
 */
function currentUserId(): int
{
    SessionManager::start();

    return (int) SessionManager::get('user_id', 0);
}
