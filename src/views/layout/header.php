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
$categorieHeader = [];
$cartItemCount = 0;

if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    \App\Foundation\FDataBase::init($GLOBALS['pdo']);
    $categorieHeader = array_map(
        static fn($categoria) => $categoria->toArray(),
        \App\Foundation\FPersistentManager::categorie()
    );

    if ($isLogged && empty($_SESSION['is_admin']) && empty($_SESSION['is_business'])) {
        $stmtCartCount = $GLOBALS['pdo']->prepare("
            SELECT COUNT(*)
            FROM carrello c
            JOIN elemento_carrello e ON e.id_carrello = c.id_carrello
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            WHERE c.id_utente = ?
              AND a.stato = 'attivo'
        ");
        $stmtCartCount->execute([(int) $_SESSION['user_id']]);
        $cartItemCount = (int) $stmtCartCount->fetchColumn();
    }
}
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title><?= e($pageTitle) ?> - NerdVault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/00-layout.css">
    <link rel="stylesheet" href="css/01-footer.css">
    <link rel="stylesheet" href="css/10-home.css">
    <link rel="stylesheet" href="css/20-annunci-form.css">
    <link rel="stylesheet" href="css/30-profilo.css">
    <link rel="stylesheet" href="css/31-venditore.css">
    <link rel="stylesheet" href="css/90-utilities.css">
</head>

<body>

<header class="site-header">
    <div class="container header-inner">
        <div class="header-top">
            <nav class="menu">
                <a class="u-style-026" href="index.php?route=home" title="Home" aria-label="Home">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
                </a>

                <?php if ($isLogged): ?>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <?php if ((int)($_SESSION['livello_sicurezza'] ?? 1) === 2): ?>
                            <a href="index.php?route=admin-dashboard">Dashboard</a>
                        <?php endif; ?>
                        <a href="index.php?route=admin-utenti">Utenti</a>
                        <a href="index.php?route=admin-segnalazioni">Segnalazioni</a>
                    <?php elseif (!empty($_SESSION['is_business'])): ?>
                        <a href="index.php?route=business">Business</a>
                    <?php else: ?>
                        <a class="cart-icon-link" href="index.php?route=carrello" aria-label="Carrello: <?= e((string)$cartItemCount) ?> prodotti">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                            <span class="cart-count-badge"><?= e((string)$cartItemCount) ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (empty($_SESSION['is_admin']) && empty($_SESSION['is_business'])): ?>
                        <div class="profile-menu">
                            <a class="profile-trigger" href="index.php?route=profilo" aria-haspopup="true" aria-expanded="false">
                                <?php if (!empty($_SESSION['propic'])): ?>
                                    <img class="profile-avatar" src="<?= e($_SESSION['propic']) ?>" alt="Foto profilo">
                                <?php else: ?>
                                    <span class="profile-avatar-fallback" aria-hidden="true">&#128100;</span>
                                <?php endif; ?>
                                <span><?= e($_SESSION['username'] ?? '') ?></span>
                            </a>
                            <div class="profile-dropdown">
                                <a href="index.php?route=wishlist">
                                    <svg class="dropdown-heart" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"></path></svg>
                                    <span>Wishlist</span>
                                </a>
                                <a href="index.php?route=logout">
                                    <svg class="dropdown-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?route=logout">Logout</a>
                        <?php if (empty($_SESSION['is_admin'])): ?>
                            <a class="u-style-027" href="index.php?route=profilo"
                              >
                            <?php if (!empty($_SESSION['propic'])): ?>
                                <img class="profile-avatar" src="<?= e($_SESSION['propic']) ?>" alt="Foto profilo">
                            <?php else: ?>
                                <span class="u-style-028">👤</span>
                            <?php endif; ?>
                            <span><?= e($_SESSION['username'] ?? '') ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php?route=login">Login</a>
                    <a href="index.php?route=register">Registrati</a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="header-main">
            <a class="logo" href="index.php?route=home" aria-label="NerdVault home">
                <img class="logo-wordmark" src="assets/img/nerdvault-wordmark.png" alt="NerdVault">
            </a>

            <form class="search-form" method="GET" action="index.php">
                <input type="hidden" name="route" value="home">

                <div class="search-input-group">
                    <input 
                        type="search" 
                        name="q" 
                        placeholder="Cerca annunci..."
                        value="<?= e($_GET['q'] ?? '') ?>"
                    >

                    <select name="id_categoria" aria-label="Categoria">
                        <option value="">Tutte le categorie</option>
                        <?php foreach ($categorieHeader as $categoria): ?>
                            <option
                                value="<?= e($categoria['id_categoria'] ?? '') ?>"
                                <?= (int)($_GET['id_categoria'] ?? 0) === (int)($categoria['id_categoria'] ?? 0) ? 'selected' : '' ?>>
                                <?= e($categoria['nome'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" aria-label="Cerca annunci">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</header>

<main class="container">
