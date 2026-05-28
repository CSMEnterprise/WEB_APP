<?php

namespace App\Middleware;

function requireAdmin(): void
{
    requireAuth();

    if (empty($_SESSION['is_admin'])) {
        http_response_code(403);
        echo 'Accesso negato: area riservata agli amministratori.';
        exit;
    }
}

function requireAdminLivello2(): void
{
    requireAdmin();

    if ((int) ($_SESSION['livello_sicurezza'] ?? 1) !== 2) {
        http_response_code(403);
        echo 'Accesso negato: area riservata agli amministratori di livello 2.';
        exit;
    }
}

function denyAdmin(): void
{
    if (!empty($_SESSION['is_admin'])) {
        http_response_code(403);
        echo 'Accesso negato: gli amministratori non possono usare questa funzione.';
        exit;
    }
}

