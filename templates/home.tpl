{* Home page / pagina di ricerca: banner promozionale per ospiti, sezione utenti trovati (solo con query testo), grid annunci con filtri avanzati (prezzo, ordinamento) e paginazione. *}
{include file="layouts/header.tpl"}

{* Il banner viene nascosto se l'utente è loggato o se sta vedendo risultati di ricerca *}
{if !$isRicerca && !$isLogged}
    <section class="card">
        <h1>Compra e vendi articoli nerd in modo semplice.</h1>
        <p>NerdVault e un marketplace per videogiochi, fumetti, action figure, carte collezionabili, gadget e prodotti da collezione.</p>
        <p><a class="btn btn-secondary" href="index.php?route=register">Crea account</a></p>
    </section>
{/if}

{if $q != '' && !empty($utenti)}
    <section class="u-style-002">
        <h2>Utenti trovati</h2>
        <div class="grid">
            {foreach $utenti as $utente}
                {include file="components/utente_card.tpl" utente=$utente}
            {/foreach}
        </div>
    </section>
{/if}

<section class="u-style-001">
    <div class="nav u-style-023">
        <h2>{$homeTitoloAnnunci|default:'Annunci in evidenza'}</h2>
        <button
            class="btn btn-secondary home-filter-toggle"
            type="button"
            id="homeFilterToggle"
            aria-label="Filtri"
            aria-controls="homeFilterPanel"
            aria-expanded="{if $hasFiltriAvanzati}true{else}false{/if}"
            title="Filtri">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 5h18"></path>
                <path d="M6 12h12"></path>
                <path d="M10 19h4"></path>
            </svg>
        </button>
    </div>

    <div class="card home-filter-panel" id="homeFilterPanel" {if !$hasFiltriAvanzati}hidden{/if}>
        <form class="home-filter-form" method="get" action="index.php">
            <input type="hidden" name="route" value="home">
            <input type="hidden" name="q" value="{$q|default:''}">
            {if $idCategoria > 0}
                <input type="hidden" name="id_categoria" value="{$idCategoria}">
            {/if}

            <div class="home-filter-field">
                <label for="prezzo_min">Prezzo minimo</label>
                <input type="number" id="prezzo_min" name="prezzo_min" min="0" step="0.01" value="{$prezzoMinValue|default:''}">
            </div>

            <div class="home-filter-field">
                <label for="prezzo_max">Prezzo massimo</label>
                <input type="number" id="prezzo_max" name="prezzo_max" min="0" step="0.01" value="{$prezzoMaxValue|default:''}">
            </div>

            <div class="home-filter-field">
                <label for="ordinamento">Ordina per</label>
                <select id="ordinamento" name="ordinamento">
                    <option value="data_desc" {if $ordinamento == 'data_desc'}selected{/if}>Piu recenti</option>
                    <option value="data_asc" {if $ordinamento == 'data_asc'}selected{/if}>Meno recenti</option>
                    <option value="prezzo_asc" {if $ordinamento == 'prezzo_asc'}selected{/if}>Prezzo crescente</option>
                    <option value="prezzo_desc" {if $ordinamento == 'prezzo_desc'}selected{/if}>Prezzo decrescente</option>
                </select>
            </div>

            <div class="home-filter-actions">
                <button class="btn" type="submit">Applica</button>
                <a class="btn btn-secondary" href="{$resetFiltersUrl}">Reset</a>
            </div>
        </form>
    </div>

    {if !empty($homeAnnunci)}
        <div class="grid">
            {foreach $homeAnnunci as $annuncio}
                {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=$carrelloIds}
            {/foreach}
        </div>

        {if !empty($pagination.show)}
            <nav class="home-pagination" aria-label="Paginazione annunci">
                <p class="muted home-pagination-summary">
                    Pagina {$paginaCorrente} di {$totalePagine} - {$totaleAnnunci} annunci
                </p>

                {if !empty($pagination.prev)}
                    <a class="btn btn-secondary" href="{$pagination.prev}">Precedente</a>
                {/if}

                {foreach $pagination.pages as $page}
                    {if !empty($page.ellipsis)}
                        <span class="muted">...</span>
                    {elseif !empty($page.active)}
                        <span class="btn home-pagination-current" aria-current="page">{$page.number}</span>
                    {else}
                        <a class="btn btn-secondary" href="{$page.url}">{$page.number}</a>
                    {/if}
                {/foreach}

                {if !empty($pagination.next)}
                    <a class="btn btn-secondary" href="{$pagination.next}">Successiva</a>
                {/if}
            </nav>
        {/if}
    {else}
        <div class="card">
            <p>Nessun annuncio disponibile al momento.</p>
        </div>
    {/if}
</section>

{* JS filtri: toggle show/hide del pannello filtri avanzati tramite attributo hidden *}
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
});
</script>

{include file="layouts/footer.tpl"}
