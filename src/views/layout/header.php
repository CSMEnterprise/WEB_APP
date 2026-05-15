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

if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    require_once __DIR__ . '/../../services/CategoryService.php';
    $categorieHeader = (new CategoryService($GLOBALS['pdo']))->getAll();
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

    <style>
        /* ── VARIABILI ─────────────────────────────────────── */
        :root {
            --bg:        #0b0b14;
            --bg-card:   #12121f;
            --bg-input:  #1a1a2e;
            --border:    #2a2a45;
            --accent:    #7c3aed;
            --accent-h:  #9d5cf6;
            --gold:      #f59e0b;
            --gold-h:    #fbbf24;
            --danger:    #ef4444;
            --text:      #f0f0ff;
            --muted:     #8b8bac;
            --radius:    12px;
        }

        /* ── RESET & BASE ──────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        a { color: var(--text); text-decoration: none; }
        h1, h2, h3 { margin-top: 0; font-weight: 700; }

        /* ── LAYOUT ────────────────────────────────────────── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        main.container { padding-top: 32px; padding-bottom: 48px; }

        /* ── HEADER ────────────────────────────────────────── */
        .site-header {
            position: sticky; top: 0; z-index: 100;
            background: rgba(11,11,20,.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .nav {
            display: flex; align-items: center;
            justify-content: space-between;
            gap: 20px; height: 68px;
        }
        .logo {
            font-weight: 800; font-size: 22px; letter-spacing: -.02em;
            background: linear-gradient(135deg, #a78bfa, var(--gold));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            white-space: nowrap;
        }
        .menu { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
        .menu a {
            color: var(--muted); font-size: 14px; font-weight: 500;
            padding: 6px 12px; border-radius: 8px;
            transition: color .2s, background .2s;
        }
        .menu a:hover { color: var(--text); background: rgba(124,58,237,.15); }

        /* ── SEARCH ────────────────────────────────────────── */
        .search-form {
            display: flex; align-items: center;
            flex: 1; max-width: 620px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden;
            transition: border-color .2s;
        }
        .search-form:focus-within { border-color: var(--accent); }
        .search-form input,
        .search-form select {
            width: 100%; margin: 0; padding: 10px 14px;
            background: transparent; border: 0;
            color: var(--text); font-family: inherit; font-size: 14px;
            outline: none;
        }
        .search-form input::placeholder { color: var(--muted); }
        .search-form select {
            border-left: 1px solid var(--border);
            max-width: 180px;
        }

        .search-form button {
            margin: 0; padding: 10px 16px; border: 0;
            background: var(--accent); color: #fff;
            font-size: 14px; font-weight: 600; cursor: pointer;
            transition: background .2s; white-space: nowrap;
        }
        .search-form button:hover { background: var(--accent-h); }

        /* ── CARD ──────────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px; margin-bottom: 16px;
            transition: border-color .25s, box-shadow .25s;
        }
        .card:hover { border-color: rgba(124,58,237,.5); box-shadow: 0 0 24px rgba(124,58,237,.12); }

        /* ── GRID ──────────────────────────────────────────── */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }

        /* ── BUTTONS ───────────────────────────────────────── */
        .btn {
            display: inline-block; padding: 10px 18px;
            border-radius: 8px; font-weight: 600; font-size: 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-h));
            color: #fff; text-decoration: none; border: 0; cursor: pointer;
            transition: opacity .2s, transform .15s;
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .btn-secondary:hover { border-color: var(--accent); color: var(--text); background: rgba(124,58,237,.1); }
        .btn-danger { background: var(--danger); }
        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-h));
            color: #000;
        }

        /* ── ALERTS ────────────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-error   { background: rgba(239,68,68,.12);  color: #fca5a5; border: 1px solid rgba(239,68,68,.3); }
        .alert-success { background: rgba(34,197,94,.1);   color: #86efac; border: 1px solid rgba(34,197,94,.3); }

        /* ── FORMS ─────────────────────────────────────────── */
        label { display: block; font-weight: 600; font-size: 13px; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: .04em; }
        input, select, textarea {
            width: 100%; max-width: 520px;
            padding: 11px 14px; margin: 0 0 16px;
            background: var(--bg-input); color: var(--text);
            border: 1px solid var(--border); border-radius: 8px;
            font-family: inherit; font-size: 14px; outline: none;
            transition: border-color .2s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }
        select option { background: var(--bg-card); }
        .password-wrapper { display: flex; align-items: flex-start; gap: 8px; max-width: 542px; margin: 0 0 16px; }
        .password-wrapper input { flex: 1; max-width: none; margin: 0; }
        .btn-password-toggle { white-space: nowrap; padding: 11px 14px; margin: 0; }

        /* ── TABLES ────────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid rgba(42,42,69,.6); font-size: 14px; vertical-align: middle; }
        tr:hover td { background: rgba(124,58,237,.04); }

        /* ── TYPOGRAPHY ────────────────────────────────────── */
        .muted  { color: var(--muted); font-size: 13px; }
        .price  { font-size: 22px; font-weight: 800; color: var(--gold); }

        /* ── ANNUNCI ───────────────────────────────────────── */
        .annuncio-card-img {
            width: 100%; height: 200px; object-fit: cover;
            border-radius: 8px; margin-bottom: 14px;
            background: var(--bg-input);
        }
        article.card { display: flex; flex-direction: column; gap: 4px; }
        article.card h2 { font-size: 16px; font-weight: 700; margin: 0 0 2px; }
        article.card .btn, article.card .btn-secondary { margin-top: 8px; }

        .annuncio-gallery { display: flex; flex-wrap: wrap; gap: 10px; margin: 12px 0 18px; }
        .annuncio-gallery img { width: 140px; height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border); }

        /* ── PHOTO UPLOAD ──────────────────────────────────── */
        .photo-upload-box { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0 0 16px; }
        .photo-upload-btn { margin: 0; }
        .photo-preview { display: flex; flex-wrap: wrap; gap: 10px; margin: 0 0 16px; }
        .photo-preview-item { width: 82px; height: 82px; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--bg-input); }
        .photo-preview-item img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* ── CARRELLO ──────────────────────────────────────── */
        .cart-layout { display: grid; grid-template-columns: minmax(0,1fr) 300px; gap: 20px; align-items: start; }
        .cart-items { display: grid; gap: 16px; }
        .cart-summary { position: sticky; top: 88px; }
        .cart-summary-actions { display: flex; flex-direction: column; gap: 10px; align-items: stretch; }
        .cart-summary-actions .btn { text-align: center; }
        .cart-item-actions { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }

        /* ── REGISTRAZIONE ─────────────────────────────────── */
        .register-choice-page { max-width: 800px; margin: 0 auto; }
        .register-choice-list { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .register-choice-card { display: flex; flex-direction: column; justify-content: space-between; gap: 16px; }
        .register-choice-card h2 { margin: 0 0 8px; }
        .register-choice-card p { margin-top: 0; color: var(--muted); font-size: 14px; }
        .register-choice-kicker { color: var(--accent-h); font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: .06em; margin-bottom: 6px; }
        .register-benefits { margin: 12px 0 0; padding-left: 18px; color: var(--muted); font-size: 14px; }
        .register-benefits li { margin-bottom: 6px; }
        .register-choice-card .btn { align-self: flex-start; }
        .register-choice-login { margin-top: 20px; color: var(--muted); font-size: 14px; }

        /* ── PAYPAL ────────────────────────────────────────── */
        .paypal-page { display: flex; justify-content: center; padding: 40px 0; }
        .paypal-card { width: 100%; max-width: 520px; border-radius: 18px; padding: 32px; text-align: center; }
        .paypal-brand { display: inline-block; margin-bottom: 12px; font-size: 36px; font-weight: 800; color: #60a5fa; letter-spacing: -.04em; }
        .paypal-summary { text-align: left; margin: 22px 0; padding: 18px; border-radius: 10px; background: rgba(255,255,255,.03); border: 1px solid var(--border); }
        .paypal-product-img { width: 100%; max-height: 220px; object-fit: cover; border-radius: 12px; margin: 8px 0 14px; background: var(--bg-input); }
        .paypal-actions { display: flex; flex-direction: column; gap: 10px; align-items: stretch; margin-top: 20px; }
        .paypal-actions .btn { text-align: center; width: 100%; }
        .paypal-confirm-btn { background: linear-gradient(135deg,#0070ba,#00a0dc); }
        .paypal-confirm-btn:hover { opacity: .9; }

        /* ── FOOTER ────────────────────────────────────────── */
        .site-footer {
            border-top: 1px solid var(--border);
            background: var(--bg-card);
            padding: 28px 0;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
        .site-footer a { color: var(--muted); }
        .site-footer a:hover { color: var(--text); }

        /* ── RESPONSIVE ────────────────────────────────────── */
        @media (max-width: 768px) {
            .cart-layout { grid-template-columns: 1fr; }
            .cart-summary { position: static; }
            .register-choice-list { grid-template-columns: 1fr; }
            .nav { flex-wrap: wrap; height: auto; padding: 12px 0; }
            .search-form { max-width: 100%; order: 3; flex: 0 0 100%; }
        }
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

            <button type="submit">Cerca</button>
        </form>

        <nav class="menu">
            <a href="index.php?route=home">Home</a>
            <a href="index.php?route=annunci">Annunci</a>

            <?php if ($isLogged): ?>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="index.php?route=profilo">Profilo</a>
                    <?php if ((int)($_SESSION['livello_sicurezza'] ?? 1) === 2): ?>
                        <a href="index.php?route=admin-dashboard">Dashboard</a>
                    <?php endif; ?>
                    <a href="index.php?route=admin-utenti">Utenti</a>
                    <a href="index.php?route=admin-segnalazioni">Segnalazioni</a>
                <?php else: ?>
                    <a href="index.php?route=profilo">Profilo</a>
                    <a href="index.php?route=carrello">Carrello</a>
                    <a href="index.php?route=wishlist">Wishlist</a>
                    <a href="index.php?route=business">Business</a>
                <?php endif; ?>
                <a href="index.php?route=logout">Logout</a>
                <?php if (empty($_SESSION['is_admin'])): ?>
                    <a href="index.php?route=profilo"
                       style="display:flex;align-items:center;gap:8px;text-decoration:none;color:white;
                              border-left:1px solid #374151;padding-left:12px;margin-left:4px;">
                        <?php if (!empty($_SESSION['propic'])): ?>
                            <img src="<?= e($_SESSION['propic']) ?>" alt="Foto profilo"
                                 style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #fff;">
                        <?php else: ?>
                            <span style="width:32px;height:32px;border-radius:50%;background:#4b5563;
                                         display:flex;align-items:center;justify-content:center;font-size:16px;
                                         border:2px solid #fff;">👤</span>
                        <?php endif; ?>
                        <span><?= e($_SESSION['username'] ?? '') ?></span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php?route=login">Login</a>
                <a href="index.php?route=register">Registrati</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main class="container">
