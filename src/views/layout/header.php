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
        /* ── VARIABILI (Modern Minimal Pop) ────────────────── */
        :root {
            --bg:        #09090b;
            --bg-card:   rgba(24, 24, 27, 0.4);
            --bg-input:  rgba(255, 255, 255, 0.03);
            --border:    rgba(255, 255, 255, 0.08);
            --accent:    #8b5cf6;
            --accent-h:  #a78bfa;
            --gold:      #facc15;
            --gold-h:    #fde047;
            --danger:    #fb7185;
            --text:      #f8fafc;
            --muted:     #94a3b8;
            --radius:    24px;
        }

        /* ── RESET & BASE ──────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        /* Global Glow Sfondo Pop */
        body::before {
            content: ''; position: fixed; top: -10vw; left: -10vw; width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 60%);
            pointer-events: none; z-index: -1;
        }
        body::after {
            content: ''; position: fixed; bottom: -10vw; right: -10vw; width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(236,72,153,0.08) 0%, transparent 60%);
            pointer-events: none; z-index: -1;
        }
        a { color: var(--text); text-decoration: none; transition: color 0.2s; }
        h1, h2, h3, h4 { margin-top: 0; font-weight: 800; letter-spacing: -0.03em; }

        /* ── LAYOUT ────────────────────────────────────────── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; width: 100%; }
        main.container { padding-top: 32px; padding-bottom: 48px; flex: 1; }

        /* ── HEADER ────────────────────────────────────────── */
        .site-header {
            position: sticky; top: 0; z-index: 100;
            background: rgba(9, 9, 11, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border);
        }
        .header-inner {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 12px 24px 20px;
        }
        .header-top {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .header-main {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        .logo {
            font-weight: 800; font-size: 26px; letter-spacing: -.02em;
            color: #ffffff;
            white-space: nowrap;
            text-decoration: none;
        }
        .menu { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
        .menu a {
            color: var(--muted); font-size: 14px; font-weight: 500;
            padding: 6px 12px; border-radius: 8px;
            transition: color .2s, background .2s;
        }
        .menu a:hover { color: var(--text); background: rgba(124,58,237,.15); }

        /* ── SEARCH ────────────────────────────────────────── */
        .search-form {
            display: flex; align-items: stretch;
            flex: 1; max-width: none; gap: 12px;
        }
        .search-input-group {
            display: flex; align-items: center;
            flex: 1;
            background: var(--bg-input);
            border: 2px solid transparent;
            border-radius: 16px;
            overflow: hidden;
            transition: all .3s;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-input-group:focus-within { border-color: var(--accent); background: rgba(0,0,0,0.2); }
        .search-input-group input {
            flex: 1;
            width: 100%; max-width: none; margin: 0; padding: 14px 16px;
            background: transparent; border: 0;
            color: var(--text); font-family: inherit; font-size: 15px;
            outline: none; box-shadow: none;
        }
        .search-input-group input:focus { border-color: transparent; background: transparent; }
        .search-input-group input::placeholder { color: var(--muted); }
        .search-input-group select {
            width: auto; margin: 0; padding: 14px 16px;
            background: transparent; border: 0;
            border-left: 1px solid var(--border);
            color: var(--text); font-family: inherit; font-size: 14px; font-weight: 600;
            outline: none; cursor: pointer; box-shadow: none;
            max-width: 180px;
        }
        .search-input-group select:focus { border-color: transparent; background: transparent; }

        .search-form button {
            margin: 0; padding: 14px 24px; border: 0;
            background: var(--accent); color: #fff;
            font-size: 15px; font-weight: 700; cursor: pointer;
            border-radius: 16px;
            transition: all .2s; white-space: nowrap;
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.25);
        }
        .search-form button:hover { background: var(--accent-h); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(139, 92, 246, 0.4); }

        /* ── CARD ──────────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px; margin-bottom: 24px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.3);
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .card:hover { border-color: rgba(139,92,246,0.3); box-shadow: 0 20px 48px rgba(0,0,0,0.4), 0 0 0 1px rgba(139,92,246,0.2); }
        .clickable-card { cursor: pointer; }
        .clickable-card:hover { transform: translateY(-4px); }
        .clickable-card:focus {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }
        .annuncio-card { position: relative; }
        .wishlist-heart {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(18,18,31,.9);
            border: 1px solid var(--border);
            color: #ffffff;
            font-size: 22px;
            line-height: 1;
            z-index: 2;
            transition: background .2s, color .2s, transform .15s, border-color .2s;
        }
        .wishlist-heart:hover {
            background: rgba(239,68,68,.18);
            border-color: rgba(239,68,68,.55);
            color: #ef4444;
            transform: scale(1.06);
        }
        .wishlist-heart-active {
            background: rgba(239,68,68,.2);
            border-color: rgba(239,68,68,.7);
            color: #ef4444;
        }

        /* ── GRID ──────────────────────────────────────────── */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }

        /* ── BUTTONS ───────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; padding: 14px 24px;
            border-radius: 16px; font-weight: 700; font-size: 15px; font-family: inherit; letter-spacing: 0.02em;
            background: var(--accent);
            color: #fff; text-decoration: none; border: none; cursor: pointer;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 14px rgba(139, 92, 246, 0.15);
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139, 92, 246, 0.25); background: var(--accent-h); }
        .btn:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15); }
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text);
            box-shadow: none;
        }
        .btn-secondary:hover { border-color: var(--accent); color: #fff; background: rgba(139, 92, 246, 0.1); box-shadow: none; }
        .btn-danger { background: var(--danger); box-shadow: 0 4px 14px rgba(251, 113, 133, 0.15); border: none; }
        .btn-danger:hover { background: #fda4af; box-shadow: 0 6px 20px rgba(251, 113, 133, 0.25); }
        .btn-gold { background: var(--gold); color: #000; box-shadow: 0 4px 14px rgba(250, 204, 21, 0.15); border: none; }
        .btn-gold:hover { background: var(--gold-h); box-shadow: 0 6px 20px rgba(250, 204, 21, 0.25); }

        /* ── ALERTS ────────────────────────────────────────── */
        .alert { padding: 16px 20px; border-radius: 16px; margin-bottom: 20px; font-size: 15px; font-weight: 600; }
        .alert-error   { background: rgba(251,113,133,.15);  color: #fda4af; border: none; }
        .alert-success { background: rgba(74,222,128,.15);   color: #86efac; border: none; }

        /* ── FORMS ─────────────────────────────────────────── */
        label { display: block; font-weight: 700; font-size: 12px; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .05em; }
        input, select, textarea {
            width: 100%; max-width: 520px;
            padding: 16px 20px; margin: 0 0 20px;
            background: var(--bg-input); color: var(--text);
            border: 2px solid transparent; border-radius: 16px;
            font-family: inherit; font-size: 15px; outline: none;
            transition: all .3s;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); background: rgba(0,0,0,0.2); }
        select option { background: var(--bg-card); }
        .password-wrapper { display: flex; align-items: flex-start; gap: 8px; max-width: 542px; margin: 0 0 20px; position: relative; }
        .password-wrapper input { flex: 1; max-width: none; margin: 0; }
        .btn-password-toggle { white-space: nowrap; padding: 12px 16px; margin: 0; box-shadow: none !important; }

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
            .header-inner { padding: 12px 0; gap: 16px; }
            .header-top { justify-content: center; }
            .header-main { flex-direction: column; align-items: stretch; gap: 12px; }
            .logo { text-align: center; }
            .search-form { flex-direction: column; }
            .search-input-group { flex-direction: column; width: 100%; }
            .search-input-group select { border-left: none; border-top: 1px solid var(--border); max-width: 100%; width: 100%; }
            .search-form button { width: 100%; }
        }
    </style>
</head>

<body>

<header class="site-header">
    <div class="container header-inner">

        <div class="header-top">
            <nav class="menu">
                <a href="index.php?route=home">Home</a>
                <a href="index.php?route=annunci">Annunci</a>

                <?php if ($isLogged): ?>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <?php if ((int)($_SESSION['livello_sicurezza'] ?? 1) === 2): ?>
                            <a href="index.php?route=admin-dashboard">Dashboard</a>
                        <?php endif; ?>
                        <a href="index.php?route=admin-utenti">Utenti</a>
                        <a href="index.php?route=admin-segnalazioni">Segnalazioni</a>
                    <?php else: ?>
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

        <div class="header-main">
            <a class="logo" href="index.php?route=home">NerdVault</a>

            <form class="search-form" method="GET" action="index.php">
                <input type="hidden" name="route" value="annunci">

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
                </div>

                <button type="submit">Cerca</button>
            </form>
        </div>

    </div>
</header>

<main class="container">
