<?php

namespace App\Middleware;

function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: /utente/profilo');
        exit;
    }
}
