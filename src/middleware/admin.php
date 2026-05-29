<?php

namespace App\Middleware;

/**
 * Verifica che l'utente loggato sia un amministratore.
 * Chiama prima requireAuth(), poi controlla `is_admin` in sessione.
 * In caso negativo risponde 403 e termina.
 */
function requireAdmin(): void
{
    requireAuth();

    if (empty($_SESSION['is_admin'])) {
        http_response_code(403);
        echo 'Accesso negato: area riservata agli amministratori.';
        exit;
    }
}

/**
 * Verifica che l'admin loggato abbia livello di sicurezza 2 (privilegio massimo).
 * Usare per route particolarmente sensibili (es. gestione altri admin, verifica business).
 * Chiama requireAdmin() internamente, quindi verifica anche l'autenticazione base.
 */
function requireAdminLivello2(): void
{
    requireAdmin();

    if ((int) ($_SESSION['livello_sicurezza'] ?? 1) !== 2) {
        http_response_code(403);
        echo 'Accesso negato: area riservata agli amministratori di livello 2.';
        exit;
    }
}

/**
 * Blocca l'accesso agli amministratori a funzionalità riservate agli utenti normali.
 * Usare sulle route di carrello, wishlist e acquisto per impedire che un admin operi
 * come compratore (separazione dei ruoli).
 */
function denyAdmin(): void
{
    if (!empty($_SESSION['is_admin'])) {
        http_response_code(403);
        echo 'Accesso negato: gli amministratori non possono usare questa funzione.';
        exit;
    }
}
