<?php

namespace App\Middleware;

use App\Core\SessionManager;
use App\Foundation\FPersistentManager;

/**
 * Verifica che l'utente loggato abbia un account business attivo.
 * Prima controlla l'autenticazione (requireAuth), poi cerca il profilo business
 * associato all'utente nel DB. Se non esiste, reindirizza alla pagina di creazione.
 * Usare sulle route riservate ai venditori business (es. gestione annunci aziendali).
 */
function requireBusiness(): void
{
    requireAuth();

    $business = FPersistentManager::businessByUser(currentUserId());

    if (!$business) {
        header('Location: /business/create');
        exit;
    }
}

/**
 * Blocca l'accesso agli account business alle funzionalità riservate agli utenti privati.
 * Gli account business sono abilitati solo alla vendita: carrello, wishlist e acquisto
 * non sono disponibili per loro (separazione dei ruoli compratore/venditore).
 */
function denyBusiness(): void
{
    if (SessionManager::has('is_business')) {
        http_response_code(403);
        echo 'Gli account business sono abilitati solo alla vendita: carrello, wishlist e acquisto prodotti non sono disponibili.';
        exit;
    }
}
