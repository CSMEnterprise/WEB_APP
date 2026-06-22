{* Home page / pagina di ricerca: allineata alla home di NerdVault Pages.html, con stessa interfaccia per ospiti e utenti loggati. *}
{include file="layouts/header.tpl"}

<div class="va-root">
    <section class="va-hero">
        <div class="va-hero-text">
            <span class="nv-chip" data-tone="accent">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"></path></svg>
                Marketplace nerd
            </span>
            <h1>Compra e vendi articoli da collezione,<br>tra appassionati.</h1>
            <p>Manga, action figure, carte collezionabili, gaming retro e gadget. Senza intermediari, con feedback verificati.</p>
            <div class="va-hero-actions">
                <a class="btn" data-size="lg" href="/annuncio/list">Esplora annunci</a>
                {if $isLogged && !$isAdmin}
                    <a class="btn" data-variant="ghost" data-size="lg" href="/annuncio/create">Vendi un oggetto</a>
                {else}
                    <a class="btn" data-variant="ghost" data-size="lg" href="/auth/register">Crea account</a>
                {/if}
            </div>
        </div>
        <div class="va-hero-stats">
            <div><strong>{if !empty($totaleAnnunci)}{$totaleAnnunci}{else}0{/if}</strong><span>annunci attivi</span></div>
            <div><strong>{if !empty($categorieHeader)}{$categorieHeader|count_items}{else}0{/if}</strong><span>categorie</span></div>
            <div><strong>4.8</strong><span>rating medio</span></div>
        </div>
    </section>

    {if !empty($categorieHeader)}
        <section class="va-cats-block">
            <div class="va-section-head">
                <h2>Categorie</h2>
                <button class="va-link va-cats-toggle" type="button" id="homeCatsToggle" aria-expanded="false" aria-controls="homeCats">
                    <span data-cats-toggle-text>Vedi tutte</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </div>
            <div class="va-cats" id="homeCats" data-collapsed="true">
                <a class="va-cat-pill {if !($idCategoria > 0)}is-active{/if}" href="/home/index{if $q != ''}?q={$q|escape:'url'}{/if}">
                    Tutte <span class="va-cat-count">{if !empty($totaleAnnunci)}{$totaleAnnunci}{else}0{/if}</span>
                </a>
                {foreach $categorieHeader as $cat}
                    <a class="va-cat-pill {if ($idCategoria|default:0) == $cat.id_categoria}is-active{/if} {if $cat@iteration > 4 && ($idCategoria|default:0) != $cat.id_categoria}is-extra{/if}"
                       href="/home/index?id_categoria={$cat.id_categoria}{if $q != ''}&q={$q|escape:'url'}{/if}">
                        <span class="va-cat-glyph">{$cat.nome|truncate:3:"":true|upper}</span>
                        {$cat.nome}
                    </a>
                {/foreach}
            </div>
        </section>
    {/if}

    {if $q != '' && !empty($utenti)}
        <section class="va-listing va-users-found">
            <div class="va-listing-head">
                <div>
                    <h2>Utenti trovati</h2>
                    <p class="va-muted">Risultati per "{$q}"</p>
                </div>
            </div>
            <div class="grid">
                {foreach $utenti as $utente}
                    {include file="components/utente_card.tpl" utente=$utente}
                {/foreach}
            </div>
        </section>
    {/if}

    <section class="va-listing" id="homeAnnunci">
        <div class="va-listing-head">
            <div>
                <h2>{$homeTitoloAnnunci|default:'Annunci in evidenza'}</h2>
                <p class="va-muted">
                    {if !empty($totaleAnnunci)}{$totaleAnnunci} risultati{else}Nessun risultato{/if}
                    {if !empty($ordinamento)}
                        &middot; ordinati per
                        {if $ordinamento == 'data_desc'}piu recenti{elseif $ordinamento == 'data_asc'}meno recenti{elseif $ordinamento == 'prezzo_asc'}prezzo crescente{elseif $ordinamento == 'prezzo_desc'}prezzo decrescente{/if}
                    {/if}
                </p>
            </div>
            <div class="va-listing-controls">
                <button
                    class="btn"
                    data-variant="{if $hasFiltriAvanzati}dark{else}ghost{/if}"
                    data-size="sm"
                    type="button"
                    id="homeFilterToggle"
                    aria-label="Filtri"
                    aria-controls="homeFilterPanel"
                    aria-expanded="{if $hasFiltriAvanzati}true{else}false{/if}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18"></path><path d="M6 12h12"></path><path d="M10 19h4"></path></svg>
                    Filtri
                </button>
                <form class="va-sort-form" method="get" action="/home/index">
                    <input type="hidden" name="q" value="{$q|default:''}">
                    {if $idCategoria > 0}<input type="hidden" name="id_categoria" value="{$idCategoria}">{/if}
                    {if $prezzoMinValue != ''}<input type="hidden" name="prezzo_min" value="{$prezzoMinValue}">{/if}
                    {if $prezzoMaxValue != ''}<input type="hidden" name="prezzo_max" value="{$prezzoMaxValue}">{/if}
                    <div class="va-select-wrap">
                        <select id="homeSort" name="ordinamento" aria-label="Ordinamento" onchange="this.form.submit()">
                            <option value="data_desc" {if $ordinamento == 'data_desc'}selected{/if}>Piu recenti</option>
                            <option value="data_asc" {if $ordinamento == 'data_asc'}selected{/if}>Meno recenti</option>
                            <option value="prezzo_asc" {if $ordinamento == 'prezzo_asc'}selected{/if}>Prezzo crescente</option>
                            <option value="prezzo_desc" {if $ordinamento == 'prezzo_desc'}selected{/if}>Prezzo decrescente</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                    </div>
                </form>
            </div>
        </div>

        <div class="va-filter-panel" id="homeFilterPanel" {if !$hasFiltriAvanzati}hidden{/if}>
            <form class="va-filter-form" method="get" action="/home/index">
                <input type="hidden" name="q" value="{$q|default:''}">
                {if $idCategoria > 0}<input type="hidden" name="id_categoria" value="{$idCategoria}">{/if}

                <div class="va-filter-field">
                    <label for="prezzo_min">Prezzo minimo</label>
                    <div class="va-input-wrap"><span>&euro;</span><input type="number" id="prezzo_min" name="prezzo_min" min="0" step="0.01" value="{$prezzoMinValue|default:''}" placeholder="0"></div>
                </div>

                <div class="va-filter-field">
                    <label for="prezzo_max">Prezzo massimo</label>
                    <div class="va-input-wrap"><span>&euro;</span><input type="number" id="prezzo_max" name="prezzo_max" min="0" step="0.01" value="{$prezzoMaxValue|default:''}" placeholder="-"></div>
                </div>

                <div class="va-filter-field">
                    <label for="ordinamento">Ordina per</label>
                    <select id="ordinamento" name="ordinamento">
                        <option value="data_desc" {if $ordinamento == 'data_desc'}selected{/if}>Piu recenti</option>
                        <option value="data_asc" {if $ordinamento == 'data_asc'}selected{/if}>Meno recenti</option>
                        <option value="prezzo_asc" {if $ordinamento == 'prezzo_asc'}selected{/if}>Prezzo crescente</option>
                        <option value="prezzo_desc" {if $ordinamento == 'prezzo_desc'}selected{/if}>Prezzo decrescente</option>
                    </select>
                </div>

                <div class="va-filter-actions">
                    <button class="btn" data-size="sm" type="submit">Applica</button>
                    <a class="btn" data-variant="ghost" data-size="sm" href="{$resetFiltersUrl}">Reset</a>
                </div>
            </form>
        </div>

        {if !empty($homeAnnunci)}
            <div class="grid va-home-grid">
                {foreach $homeAnnunci as $annuncio}
                    {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=$carrelloIds}
                {/foreach}
            </div>

            {if !empty($pagination.show)}
                <nav class="va-pagination" aria-label="Paginazione annunci">
                    {if !empty($pagination.prev)}
                        <a class="btn" data-variant="ghost" data-size="sm" href="{$pagination.prev}">Precedente</a>
                    {/if}

                    {foreach $pagination.pages as $page}
                        {if !empty($page.ellipsis)}
                            <span class="va-muted">...</span>
                        {elseif !empty($page.active)}
                            <span class="va-page is-active" aria-current="page">{$page.number}</span>
                        {else}
                            <a class="va-page" href="{$page.url}">{$page.number}</a>
                        {/if}
                    {/foreach}

                    {if !empty($pagination.next)}
                        <a class="btn" data-variant="ghost" data-size="sm" href="{$pagination.next}">Successiva</a>
                    {/if}
                </nav>
            {/if}
        {else}
            <div class="pg-card va-empty">
                <h3>Nessun annuncio disponibile al momento.</h3>
                <p class="va-muted">Torna piu tardi o prova a cambiare i filtri.</p>
            </div>
        {/if}
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('homeFilterToggle');
    const panel = document.getElementById('homeFilterPanel');

    if (!toggle || !panel) return;

    toggle.addEventListener('click', function () {
        const isHidden = panel.hasAttribute('hidden');
        panel.toggleAttribute('hidden', !isHidden);
        toggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
    });

    const cats = document.getElementById('homeCats');
    const catsToggle = document.getElementById('homeCatsToggle');
    const catsToggleText = catsToggle ? catsToggle.querySelector('[data-cats-toggle-text]') : null;

    if (cats && catsToggle && catsToggleText) {
        catsToggle.addEventListener('click', function () {
            const isCollapsed = cats.getAttribute('data-collapsed') !== 'false';
            cats.setAttribute('data-collapsed', isCollapsed ? 'false' : 'true');
            catsToggle.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
            catsToggleText.textContent = isCollapsed ? 'Mostra meno' : 'Vedi tutte';
        });
    }
});
</script>

{include file="layouts/footer.tpl"}
