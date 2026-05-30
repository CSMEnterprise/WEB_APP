{* Wishlist in stile NerdVault Pages.html: header con cuore, toolbar, vista griglia/lista e stato vuoto. *}
{include file="layouts/header.tpl"}

{assign var=wishlistCount value=$wishlist|count_items}
{assign var=wishlistTotal value=0}
{if !empty($wishlist)}
    {foreach $wishlist as $annuncio}
        {assign var=wishlistTotal value=$wishlistTotal + ($annuncio.prezzo|default:0)}
    {/foreach}
{/if}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">Wishlist</span>
</nav>

<div class="pg-head wl-head">
    <div>
        <div class="wl-title-row">
            <span class="wl-heart-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 0 1 12 6a5 5 0 0 1 7.5 6.6z"></path></svg>
            </span>
            <h1 class="pg-h1">La mia wishlist</h1>
        </div>
        <p class="pg-sub">
            {if $wishlistCount > 0}
                {$wishlistCount} articol{if $wishlistCount == 1}o salvato{else}i salvati{/if} &middot; totale &euro; {$wishlistTotal|number_format:2:",":"."}
            {else}
                Salva qui gli annunci che vuoi tenere d'occhio.
            {/if}
        </p>
    </div>

    <div class="wl-toolbar">
        <div class="pg-tabs wl-tabs" aria-label="Filtri wishlist">
            <button class="pg-tab is-active" type="button">Tutti <span class="pg-tab-count">{$wishlistCount}</span></button>
        </div>
        <div class="wl-viewtoggle" aria-label="Cambia vista">
            <button class="is-on" type="button" data-wl-view="grid" aria-label="Vista griglia">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"></rect><rect x="13" y="3" width="8" height="8" rx="1.5"></rect><rect x="3" y="13" width="8" height="8" rx="1.5"></rect><rect x="13" y="13" width="8" height="8" rx="1.5"></rect></svg>
            </button>
            <button type="button" data-wl-view="list" aria-label="Vista lista">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"></path></svg>
            </button>
        </div>
        {if $wishlistCount > 0}
            <a class="btn btn-secondary" data-size="sm" href="/wishlist/clear">Svuota wishlist</a>
        {/if}
    </div>
</div>

{if !empty($wishlist)}
    <section class="wl-panel" data-wl-panel>
        <div class="va-grid wl-grid" data-wl-grid>
            {foreach $wishlist as $annuncio}
                {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=$carrelloIds}
            {/foreach}
        </div>

        <div class="wl-list" data-wl-list hidden>
            {foreach $wishlist as $annuncio}
                {assign var=annuncioId value=$annuncio.id_annuncio|default:0}
                {if !empty($annuncio.venditore_business_id)}
                    {assign var=sellerName value=$annuncio.venditore_nome_azienda|default:'Venditore'}
                {else}
                    {assign var=sellerName value=$annuncio.venditore_username|default:'Venditore'}
                {/if}
                {assign var=isInCart value=$annuncioId|in_array:$carrelloIds}
                <article class="wl-row">
                    <a class="wl-row-img" href="/annuncio/show/{$annuncioId}">
                        {if !empty($annuncio.immagine_principale)}
                            <img src="{$annuncio.immagine_principale}" alt="Foto annuncio">
                        {else}
                            <span>Nessuna foto</span>
                        {/if}
                    </a>
                    <div class="wl-row-body">
                        <div class="va-card-meta">
                            <span class="va-card-cat">{$annuncio.categoria_nome|default:'Senza categoria'}</span>
                            {if !empty($annuncio.stato_conservazione)}<span>&middot; {$annuncio.stato_conservazione}</span>{/if}
                        </div>
                        <h3 class="wl-row-title"><a href="/annuncio/show/{$annuncioId}">{$annuncio.titolo|default:'Annuncio'}</a></h3>
                        <div class="wl-row-seller">
                            <span>{$sellerName}</span>
                            {if !empty($annuncio.venditore_business_id)}<span class="nv-pro-badge">PRO</span>{/if}
                            <span class="va-card-rating">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
                                4.8
                            </span>
                        </div>
                    </div>
                    <div class="wl-row-actions">
                        <span class="wl-row-price">&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</span>
                        {if $isInCart}
                            <span class="btn" data-size="sm" data-variant="dark">Nel carrello</span>
                        {else}
                            <a class="btn wl-cart-btn" data-size="sm" href="/carrello/add/{$annuncioId}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                Al carrello
                            </a>
                        {/if}
                        <a class="wl-remove-btn" href="/wishlist/remove/{$annuncioId}" title="Rimuovi dalla wishlist" aria-label="Rimuovi dalla wishlist">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        </a>
                    </div>
                </article>
            {/foreach}
        </div>
    </section>
{else}
    <section class="pg-card wl-empty">
        <span class="wl-empty-glyph" aria-hidden="true">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 0 1 12 6a5 5 0 0 1 7.5 6.6z"></path></svg>
        </span>
        <h2 class="pg-card-title">Nessun articolo salvato</h2>
        <p class="muted">Tocca il cuore su un annuncio per salvarlo qui.</p>
        <a class="btn" href="/home/index">Esplora annunci</a>
    </section>
{/if}

<script>
document.querySelectorAll('[data-wl-view]').forEach(function (button) {
    button.addEventListener('click', function () {
        const view = button.getAttribute('data-wl-view');
        const panel = button.closest('.pg-head').nextElementSibling;
        const grid = panel ? panel.querySelector('[data-wl-grid]') : null;
        const list = panel ? panel.querySelector('[data-wl-list]') : null;
        if (!grid || !list) return;
        grid.hidden = view !== 'grid';
        list.hidden = view !== 'list';
        button.parentElement.querySelectorAll('[data-wl-view]').forEach(function (item) {
            item.classList.toggle('is-on', item === button);
        });
    });
});
</script>

{include file="layouts/footer.tpl"}
