<?php

require_once __DIR__ . '/auth.php';

function requireAdmin(): void
{
    requireAuth();

    if (empty($_SESSION['is_admin'])) {
        http_response_code(403);
        echo 'Accesso negato: area riservata agli amministratori.';
        exit;
    }
}
