<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../services/BusinessService.php';

function requireBusiness(PDO $pdo): void
{
    requireAuth();

    $businessService = new BusinessService($pdo);
    $business = $businessService->findByUserId(currentUserId());

    if (!$business) {
        header('Location: index.php?route=business-create');
        exit;
    }
}
