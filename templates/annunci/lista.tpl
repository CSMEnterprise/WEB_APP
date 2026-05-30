{* Lista annunci: mostra tutti gli annunci disponibili o i risultati di una ricerca per testo/categoria. Se la ricerca restituisce anche utenti, li mostra in una sezione separata. *}
{include file="layouts/header.tpl"}

{assign var=categoriaSelezionata value=''}
{if $idCategoria > 0}
    {foreach $categorie as $categoria}
        {if $categoria.id_categoria == $idCategoria}
            {assign var=categoriaSelezionata value=$categoria.nome}
        {/if}
    {/foreach}
{/if}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">{if $q != '' || $idCategoria > 0}Risultati ricerca{else}Tutti gli annunci{/if}</span>
</nav>

<div class="pg-head ls-head">
    <div>
        <h1 class="pg-h1">{if $q != '' || $idCategoria > 0}Risultati ricerca{else}Annunci disponibili{/if}</h1>
        <p class="pg-sub">
            {if !empty($annunci)}{$annunci|count_items} oggett{if $annunci|count_items == 1}o{else}i{/if} trovati{else}Nessun annuncio trovato{/if}
            {if $q != ''} per "{$q}"{/if}
            {if $categoriaSelezionata != ''} in {$categoriaSelezionata}{/if}
        </p>
    </div>

    {if $isLogged && !$isAdmin}
        <a class="btn" data-variant="gold" href="/annuncio/create">Crea annuncio</a>
    {/if}
</div>

{if $q != '' && !empty($utenti)}
    <section class="cat-rail-block">
        <div class="cat-rail-head"><h2>Utenti trovati</h2></div>
        <div class="grid">
            {foreach $utenti as $utente}
                {include file="components/utente_card.tpl" utente=$utente}
            {/foreach}
        </div>
    </section>
{/if}

<div class="ls-layout">
    <aside class="ls-sidebar">
        <form class="ls-filter-card" method="get" action="/home/index">
            <div class="ls-filter-head">
                <h3>Filtri</h3>
                {if $q != '' || $idCategoria > 0}<a class="va-link" href="/annuncio/list">Azzera</a>{/if}
            </div>

            <div class="ls-filter-group">
                <label class="ls-filter-label" for="listaQuery">Ricerca</label>
                <input class="pg-input" id="listaQuery" type="search" name="q" value="{$q|default:''}" placeholder="Cerca nel vault">
            </div>

            <div class="ls-filter-group">
                <label class="ls-filter-label" for="listaCategoria">Categoria</label>
                <select class="pg-select" id="listaCategoria" name="id_categoria">
                    <option value="">Tutte le categorie</option>
                    {foreach $categorie as $categoria}
                        <option value="{$categoria.id_categoria}" {if $idCategoria == $categoria.id_categoria}selected{/if}>{$categoria.nome}</option>
                    {/foreach}
                </select>
            </div>

            <button class="btn btn-block" type="submit">Applica filtri</button>
        </form>
    </aside>

    <section class="ls-results">
        <div class="ls-results-bar">
            <div class="ls-chips">
                {if $q == '' && $idCategoria <= 0}
                    <span class="pg-sub">Nessun filtro attivo</span>
                {else}
                    {if $q != ''}<span class="ls-active-chip">Testo: {$q}</span>{/if}
                    {if $categoriaSelezionata != ''}<span class="ls-active-chip">Categoria: {$categoriaSelezionata}</span>{/if}
                {/if}
            </div>
        </div>

        {if !empty($annunci)}
            <div class="grid ls-grid">
                {foreach $annunci as $annuncio}
                    {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=$carrelloIds}
                {/foreach}
            </div>
        {else}
            <div class="pg-card ls-empty">
                <div class="ls-empty-glyph">⌕</div>
                <h2 class="pg-card-title">Nessun annuncio trovato</h2>
                <p class="muted">Prova ad allargare i filtri o torna a tutti gli annunci.</p>
                <a class="btn btn-secondary" href="/annuncio/list">Azzera filtri</a>
            </div>
        {/if}
    </section>
</div>

{include file="layouts/footer.tpl"}
