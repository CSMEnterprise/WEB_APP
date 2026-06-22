<?php

namespace App\Middleware;

/**
 * Verifica che l'utente NON sia autenticato (guest).
 * Se è già loggato, reindirizza al profilo e termina.
 * Usare sulle route di login e registrazione per evitare che un utente
 * già autenticato acceda nuovamente a queste pagine.
 */
function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: /utente/profilo');
        exit;
    }
}
