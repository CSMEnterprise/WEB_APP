<?php

namespace App\Middleware;

function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: index.php?route=profilo');
        exit;
    }
}
