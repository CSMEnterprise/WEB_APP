{* Layout header: apre il documento HTML, carica tutti i CSS e costruisce la navbar adattiva in base al ruolo (admin, business, utente normale, ospite). Header compatto a riga singola: brand · ricerca · menu. *}
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>{$pageTitle} - NerdVault</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/00-layout.css?v=nerdvault-redesign-20260626">
    <link rel="stylesheet" href="/css/01-footer.css?v=nerdvault-redesign-20260530">
    <link rel="stylesheet" href="/css/02-components.css?v=nerdvault-redesign-20260627-fit-images">
    <link rel="stylesheet" href="/css/10-home.css?v=nerdvault-redesign-20260626">
    <link rel="stylesheet" href="/css/20-annunci-form.css?v=nerdvault-redesign-20260530">
    <link rel="stylesheet" href="/css/30-profilo.css?v=nerdvault-redesign-20260530">
    <link rel="stylesheet" href="/css/31-venditore.css?v=nerdvault-redesign-20260530">
    <link rel="stylesheet" href="/css/90-utilities.css?v=nerdvault-redesign-20260530">
</head>

<body>

<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="/home/index" aria-label="NerdVault home">
            <span class="logo-mark" aria-hidden="true"><span class="logo-mark-inner">NV</span></span>
            <span class="logo-text">Nerd<span class="logo-accent">Vault</span></span>
        </a>

        <form class="search-form" method="GET" action="/home/index">
            <div class="search-input-group">
                <svg class="search-leading-icon" viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                <input
                    type="search"
                    name="q"
                    placeholder="Cerca manga, figure, carte…"
                    value="{$get.q|default:''}">

                <span class="search-divider"></span>

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

        <nav class="menu">
            <a class="u-style-026" href="/home/index" title="Home" aria-label="Home">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
            </a>

            {* Navbar adattiva: admin vede dashboard/utenti/segnalazioni, business vede area business, utente normale vede carrello e menu profilo *}
            {if $isLogged}
                {if $isAdmin}
                    {if $livelloSicurezza == 2}
                        <a href="/admin/dashboard">Dashboard</a>
                    {/if}
                    <a href="/admin/utenti">Utenti</a>
                    <a href="/admin/segnalazioni">Segnalazioni</a>
                    <div class="profile-menu">
                        <a class="profile-trigger admin-profile-trigger" href="/admin/index" aria-haspopup="true" aria-expanded="false">
                            <span class="profile-avatar-fallback admin-avatar" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </span>
                            <span>Admin</span>
                        </a>
                        <div class="profile-dropdown">
                            <a href="/admin/index">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                <span>Home Admin</span>
                            </a>
                            <form class="u-post-form" method="post" action="/auth/logout">
                                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                                <button class="u-post-button" type="submit">
                                <svg class="dropdown-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>
                                <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                {elseif $isBusiness}
                    <a href="/annuncio/create">Vendi</a>
                    <a href="/business/dashboard">Business</a>
                {else}
                    <a href="/annuncio/create">Vendi</a>
                    <a class="cart-icon-link" href="/carrello/list" aria-label="Carrello: {$cartItemCount} prodotti">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                        <span class="cart-count-badge">{$cartItemCount}</span>
                    </a>
                {/if}

                {if !$isAdmin && !$isBusiness}
                    <div class="profile-menu">
                        <a class="profile-trigger" href="/utente/profilo" aria-haspopup="true" aria-expanded="false">
                            {if $propic}
                                <img class="profile-avatar" src="{$propic}" alt="Foto profilo">
                            {else}
                                <span class="profile-avatar-fallback" aria-hidden="true">{$username|truncate:1:"":true|upper}</span>
                            {/if}
                            <span>{$username}</span>
                        </a>
                        <div class="profile-dropdown">
                            <a href="/wishlist/list">
                                <svg class="dropdown-heart" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"></path></svg>
                                <span>Wishlist</span>
                            </a>
                            <a href="/feedback/list">
                                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.8 14.9 8.7l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9L12 2.8Z"></path></svg>
                                <span>I miei feedback</span>
                            </a>
                            <form class="u-post-form" method="post" action="/auth/logout">
                                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                                <button class="u-post-button" type="submit">
                                <svg class="dropdown-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>
                                <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                {elseif $isBusiness}
                    <div class="profile-menu">
                        <a class="profile-trigger" href="/utente/profilo" aria-haspopup="true" aria-expanded="false">
                            {if $propic}
                                <img class="profile-avatar" src="{$propic}" alt="Foto profilo">
                            {else}
                                <span class="profile-avatar-fallback" aria-hidden="true">{$username|truncate:1:"":true|upper}</span>
                            {/if}
                            <span>{$username}</span>
                        </a>
                        <div class="profile-dropdown">
                            <a href="/business/ordini">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <span>Ordini ricevuti</span>
                            </a>
                            <a href="/feedback/list">
                                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.8 14.9 8.7l6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9L12 2.8Z"></path></svg>
                                <span>I miei feedback</span>
                            </a>
                            <form class="u-post-form" method="post" action="/auth/logout">
                                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                                <button class="u-post-button" type="submit">
                                <svg class="dropdown-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>
                                <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                {/if}
            {else}
                <a href="/auth/login">Login</a>
                <a href="/auth/register">Registrati</a>
            {/if}
        </nav>
    </div>
</header>

<main class="container">
