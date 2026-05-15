<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$pageTitle = $pageTitle ?? 'NerdVault';
$isLogged = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title><?= e($pageTitle) ?> - NerdVault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; color: #222; }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .site-header, .site-footer { background: #111827; color: white; }
        .site-header a, .site-footer a { color: white; text-decoration: none; }

        .nav { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .menu { display: flex; flex-wrap: wrap; gap: 12px; }
        .logo { font-weight: bold; font-size: 22px; }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 420px;
        }

        .search-form input {
            width: 100%;
            margin: 0;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .search-form button {
            margin: 0;
            padding: 8px 12px;
            border-radius: 8px;
            border: 0;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        .card { background: white; border-radius: 10px; padding: 18px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; background: #2563eb; color: white; text-decoration: none; border: 0; cursor: pointer; }
        .btn-secondary { background: #4b5563; }
        .btn-danger { background: #dc2626; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
        input, select, textarea { width: 100%; max-width: 520px; padding: 10px; margin: 6px 0 14px; border: 1px solid #ccc; border-radius: 8px; }
        label { display: block; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .muted { color: #6b7280; }
        .price { font-size: 20px; font-weight: bold; }
    </style>
</head>

<body>

<header class="site-header">
    <div class="container nav">

        <a class="logo" href="index.php?route=home">NerdVault</a>

        <form class="search-form" method="GET" action="index.php">
            <input type="hidden" name="route" value="annunci">

            <input 
                type="search" 
                name="q" 
                placeholder="Cerca annunci..."
                value="<?= e($_GET['q'] ?? '') ?>"
            >

            <button type="submit">Cerca</button>
        </form>

        <nav class="menu">
            <a href="index.php?route=home">Home</a>
            <a href="index.php?route=annunci">Annunci</a>

            <?php if ($isLogged): ?>
                <a href="index.php?route=profilo">Profilo</a>
                <a href="index.php?route=carrello">Carrello</a>
                <a href="index.php?route=business">Business</a>
                <a href="index.php?route=logout">Logout</a>
            <?php else: ?>
                <a href="index.php?route=login">Login</a>
                <a href="index.php?route=register">Registrati</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main class="container">