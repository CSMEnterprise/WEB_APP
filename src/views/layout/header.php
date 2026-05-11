<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$baseUrl = $baseUrl ?? '/';
$pageTitle = $pageTitle ?? 'NerdVault';
$isLogged = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title><?= e($pageTitle) ?> - NerdVault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="logo" href="index.php?action=home">NerdVault</a>

        <nav class="menu">
            <a href="index.php?action=annunci">Annunci</a>
            <?php if ($isLogged): ?>
                <a href="index.php?action=profilo">Profilo</a>
                <a href="index.php?action=carrello">Carrello</a>
                <a href="index.php?action=logout">Logout</a>
            <?php else: ?>
                <a href="index.php?action=login">Login</a>
                <a class="btn btn-small" href="index.php?action=register">Registrati</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container main">
    <?php require __DIR__ . '/../partials/flash.php'; ?>
