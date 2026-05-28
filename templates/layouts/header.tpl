{* Layout header: apre il documento HTML, carica tutti i CSS e costruisce la navbar adattiva in base al ruolo (admin, business, utente normale, ospite). Contiene la barra di ricerca con filtro categoria. Ogni pagina include questo file come prima istruzione. *}
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>{$pageTitle} - NerdVault</title>
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

                {* Navbar adattiva: admin vede dashboard/utenti/segnalazioni, business vede area business, utente normale vede carrello e menu profilo *}
                {if $isLogged}
                    {if $isAdmin}
                        {if $livelloSicurezza == 2}
                            <a href="index.php?route=admin-dashboard">Dashboard</a>
                        {/if}
                        <a href="index.php?route=admin-utenti">Utenti</a>
                        <a href="index.php?route=admin-segnalazioni">Segnalazioni</a>
                        <div class="profile-menu">
                            <a class="profile-trigger admin-profile-trigger" href="index.php?route=admin" aria-haspopup="true" aria-expanded="false">
                                <span class="profile-avatar-fallback admin-avatar" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </span>
                                <span>Admin</span>
                            </a>
                            <div class="profile-dropdown">
                                <a href="index.php?route=admin">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    <span>Home Admin</span>
                                </a>
                                <a href="index.php?route=logout">
                                    <svg class="dropdown-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    {elseif $isBusiness}
                        <a href="index.php?route=business">Business</a>
                    {else}
                        <a class="cart-icon-link" href="index.php?route=carrello" aria-label="Carrello: {$cartItemCount} prodotti">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                            <span class="cart-count-badge">{$cartItemCount}</span>
                        </a>
                    {/if}

                    {if !$isAdmin && !$isBusiness}
                        <div class="profile-menu">
                            <a class="profile-trigger" href="index.php?route=profilo" aria-haspopup="true" aria-expanded="false">
                                {if $propic}
                                    <img class="profile-avatar" src="{$propic}" alt="Foto profilo">
                                {else}
                                    <span class="profile-avatar-fallback" aria-hidden="true">&#128100;</span>
                                {/if}
                                <span>{$username}</span>
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
                    {elseif $isBusiness}
                        <a href="index.php?route=logout">Logout</a>
                        <a class="u-style-027" href="index.php?route=profilo">
                            {if $propic}
                                <img class="profile-avatar" src="{$propic}" alt="Foto profilo">
                            {else}
                                <span class="u-style-028">👤</span>
                            {/if}
                            <span>{$username}</span>
                        </a>
                    {/if}
                {else}
                    <a href="index.php?route=login">Login</a>
                    <a href="index.php?route=register">Registrati</a>
                {/if}
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
                        value="{$get.q|default:''}">

                    <select name="id_categoria" aria-label="Categoria">
                        <option value="">Tutte le categorie</option>
                        {foreach $categorieHeader as $cat}
                            <option value="{$cat.id_categoria}"
                                {if ($get.id_categoria|default:0) == $cat.id_categoria}selected{/if}>
                                {$cat.nome}
                            </option>
                        {/foreach}
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
