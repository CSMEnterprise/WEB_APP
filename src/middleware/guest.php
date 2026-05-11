<?php

require_once __DIR__ . '/auth.php';

function requireGuest(): void
{
    if (isLoggedIn()) {
        header('Location: index.php?route=profilo');
        exit;
    }
}
