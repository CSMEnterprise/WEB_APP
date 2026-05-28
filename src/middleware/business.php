<?php

namespace App\Middleware;

use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use PDO;

function requireBusiness(PDO $pdo): void
{
    requireAuth();

    FDataBase::init($pdo);
    $business = FPersistentManager::businessByUser(currentUserId());

    if (!$business) {
        header('Location: /business/create');
        exit;
    }
}

function denyBusiness(): void
{
    if (!empty($_SESSION['is_business'])) {
        http_response_code(403);
        echo 'Gli account business sono abilitati solo alla vendita: carrello, wishlist e acquisto prodotti non sono disponibili.';
        exit;
    }
}
