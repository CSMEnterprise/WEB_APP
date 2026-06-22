{* Ordini ricevuti in stile NerdVault Pages.html: KPI, filtri stato, tabella responsive ed export CSV client-side. *}
{include file="layouts/header.tpl"}

{assign var=ordiniCount value=$ordini|count_items}
{assign var=incassoTotale value=0}
{assign var=daSpedireCount value=0}
{assign var=speditiCount value=0}
{assign var=consegnatiCount value=0}
{foreach $ordini as $ordine}
    {assign var=incassoTotale value=$incassoTotale + ($ordine.importo_totale|default:0)}
    {assign var=ordineStato value=$ordine.stato|default:''|lower}
    {if $ordineStato == 'completato' || $ordineStato == 'da spedire' || $ordineStato == 'pagato'}
        {assign var=daSpedireCount value=$daSpedireCount + 1}
    {elseif $ordineStato == 'spedito'}
        {assign var=speditiCount value=$speditiCount + 1}
    {elseif $ordineStato == 'consegnato'}
        {assign var=consegnatiCount value=$consegnatiCount + 1}
    {/if}
{/foreach}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <a href="/business/profilo">Pannello venditore</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">Ordini ricevuti</span>
</nav>

<div class="pg-head or-head">
    <div>
        <h1 class="pg-h1">Ordini ricevuti</h1>
        <p class="pg-sub">Gestisci le vendite e tieni traccia delle spedizioni.</p>
    </div>
    <span class="nv-pro-badge or-head-badge">Venditore PRO</span>
</div>

<section class="or-kpis" aria-label="Riepilogo ordini">
    <div class="or-kpi">
        <span class="or-kpi-label">Incasso totale</span>
        <strong class="or-kpi-val or-kpi-gold">&euro; {$incassoTotale|number_format:2:",":"."}</strong>
        <span class="or-kpi-sub">Totale ordini ricevuti</span>
    </div>
    <div class="or-kpi">
        <span class="or-kpi-label">Ordini</span>
        <strong class="or-kpi-val">{$ordiniCount}</strong>
        <span class="or-kpi-sub">Storico vendite</span>
    </div>
    <div class="or-kpi">
        <span class="or-kpi-label">Da spedire</span>
        <strong class="or-kpi-val or-kpi-accent">{$daSpedireCount}</strong>
        <span class="or-kpi-sub">Da gestire con priorita</span>
    </div>
    <div class="or-kpi">
        <span class="or-kpi-label">Feedback medio</span>
        <strong class="or-kpi-val">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
            4.9
        </strong>
        <span class="or-kpi-sub">Feedback verificati</span>
    </div>
</section>

<div class="or-toolbar">
    <div class="or-filters" aria-label="Filtra ordini per stato">
        <button class="or-filter is-on" type="button" data-or-filter="tutti">Tutti <span class="or-filter-count">{$ordiniCount}</span></button>
        <button class="or-filter" type="button" data-or-filter="da-spedire">Da spedire <span class="or-filter-count">{$daSpedireCount}</span></button>
        <button class="or-filter" type="button" data-or-filter="spedito">Spediti <span class="or-filter-count">{$speditiCount}</span></button>
        <button class="or-filter" type="button" data-or-filter="consegnato">Consegnati <span class="or-filter-count">{$consegnatiCount}</span></button>
    </div>
    <button class="btn btn-secondary" data-size="sm" type="button" data-or-export>Esporta CSV</button>
</div>

{if !empty($ordini)}
    <section class="or-table" data-or-table>
        <div class="or-tr or-th">
            <span>Ordine</span>
            <span>Articolo</span>
            <span>Acquirente</span>
            <span>Importo</span>
            <span>Stato</span>
            <span>Data</span>
            <span></span>
        </div>

        {foreach $ordini as $ordine}
            {assign var=statoRaw value=$ordine.stato|default:'completato'}
            {assign var=statoLower value=$statoRaw|lower}
            {if $statoLower == 'completato' || $statoLower == 'pagato' || $statoLower == 'da spedire'}
                {assign var=statoGroup value='da-spedire'}
                {assign var=statoLabel value='Da spedire'}
                {assign var=statoTone value='gold'}
            {elseif $statoLower == 'spedito'}
                {assign var=statoGroup value='spedito'}
                {assign var=statoLabel value='Spedito'}
                {assign var=statoTone value=''}
            {elseif $statoLower == 'consegnato'}
                {assign var=statoGroup value='consegnato'}
                {assign var=statoLabel value='Consegnato'}
                {assign var=statoTone value='success'}
            {else}
                {assign var=statoGroup value=$statoLower}
                {assign var=statoLabel value=$statoRaw|ucfirst}
                {assign var=statoTone value=''}
            {/if}

            <article class="or-tr" data-or-row data-state="{$statoGroup}">
                <span class="or-mono" data-or-id>#{$ordine.id_pagamento|default:''}</span>
                <div class="or-article">
                    <a class="or-article-img" href="/annuncio/show/{$ordine.id_annuncio|default:0}">
                        {if !empty($ordine.immagine_principale)}
                            <img src="{$ordine.immagine_principale}" alt="Foto annuncio">
                        {else}
                            <span>NV</span>
                        {/if}
                    </a>
                    <a class="or-ellip" href="/annuncio/show/{$ordine.id_annuncio|default:0}" data-or-title>{$ordine.titolo|default:'Annuncio'}</a>
                </div>
                <div class="or-buyer">
                    <span class="or-buyer-av">{$ordine.acquirente_username|default:'U'|substr:0:1|strtoupper}</span>
                    <span data-or-buyer>{$ordine.acquirente_username|default:'Acquirente'}</span>
                </div>
                <span class="or-mono or-amount" data-or-amount>&euro; {$ordine.importo_totale|default:0|number_format:2:",":"."}</span>
                <span><span class="pg-pill" data-tone="{$statoTone}" data-or-status>{$statoLabel}</span></span>
                <span class="pg-sub" data-or-date>{$ordine.data|default:''}</span>
                <span class="or-actions">
                    {if $statoGroup == 'da-spedire'}
                        <button class="btn" data-size="sm" type="button" data-or-ship>Segna spedito</button>
                    {else}
                        <button class="btn btn-secondary" data-size="sm" type="button" data-or-details>Dettagli</button>
                    {/if}
                </span>
                <div class="or-detail" data-or-detail hidden>
                    <div><strong>ID pagamento</strong><span>#{$ordine.id_pagamento|default:''}</span></div>
                    <div><strong>Transazione</strong><span>{$ordine.paypal_transaction_id|default:'Non disponibile'}</span></div>
                    <div><strong>Indirizzo spedizione</strong><span>#{$ordine.id_indirizzo_spedizione|default:'-'}</span></div>
                </div>
            </article>
        {/foreach}

        <div class="or-empty" data-or-empty hidden>
            <p class="pg-sub">Nessun ordine in questo stato.</p>
        </div>
    </section>
{else}
    <section class="pg-card or-empty-main">
        <div class="or-empty-icon">NV</div>
        <h2 class="pg-card-title">Non ci sono ordini ricevuti</h2>
        <p class="pg-sub">Quando venderai un articolo, lo troverai qui con stato, acquirente e importo.</p>
        <a class="btn" href="/annuncio/create">Crea un annuncio</a>
    </section>
{/if}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = Array.from(document.querySelectorAll('[data-or-row]'));
    const empty = document.querySelector('[data-or-empty]');
    const filters = Array.from(document.querySelectorAll('[data-or-filter]'));

    function applyFilter(state) {
        let visible = 0;
        rows.forEach(function (row) {
            const show = state === 'tutti' || row.dataset.state === state;
            row.hidden = !show;
            if (show) visible += 1;
        });
        if (empty) empty.hidden = visible !== 0;
        filters.forEach(function (button) {
            button.classList.toggle('is-on', button.dataset.orFilter === state);
        });
    }

    filters.forEach(function (button) {
        button.addEventListener('click', function () {
            applyFilter(button.dataset.orFilter);
        });
    });

    document.querySelectorAll('[data-or-ship]').forEach(function (button) {
        button.addEventListener('click', function () {
            const row = button.closest('[data-or-row]');
            const badge = row.querySelector('[data-or-status]');
            row.dataset.state = 'spedito';
            badge.textContent = 'Spedito';
            badge.removeAttribute('data-tone');
            button.className = 'btn btn-secondary';
            button.textContent = 'Dettagli';
            button.removeAttribute('data-or-ship');
            button.setAttribute('data-or-details', '');
            applyFilter(document.querySelector('[data-or-filter].is-on')?.dataset.orFilter || 'tutti');
        });
    });

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-or-details]');
        if (!button) return;
        const row = button.closest('[data-or-row]');
        const detail = row.querySelector('[data-or-detail]');
        if (!detail) return;
        detail.hidden = !detail.hidden;
        button.textContent = detail.hidden ? 'Dettagli' : 'Chiudi';
    });

    const exportButton = document.querySelector('[data-or-export]');
    if (exportButton) {
        exportButton.addEventListener('click', function () {
            const visibleRows = rows.filter(function (row) { return !row.hidden; });
            const csvRows = [['Ordine', 'Articolo', 'Acquirente', 'Importo', 'Stato', 'Data']];
            visibleRows.forEach(function (row) {
                csvRows.push([
                    row.querySelector('[data-or-id]')?.textContent.trim() || '',
                    row.querySelector('[data-or-title]')?.textContent.trim() || '',
                    row.querySelector('[data-or-buyer]')?.textContent.trim() || '',
                    row.querySelector('[data-or-amount]')?.textContent.trim() || '',
                    row.querySelector('[data-or-status]')?.textContent.trim() || '',
                    row.querySelector('[data-or-date]')?.textContent.trim() || ''
                ]);
            });
            const csv = csvRows.map(function (cells) {
                return cells.map(function (cell) {
                    return '"' + String(cell).replaceAll('"', '""') + '"';
                }).join(',');
            }).join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'ordini-nerdvault.csv';
            document.body.appendChild(link);
            link.click();
            URL.revokeObjectURL(link.href);
            link.remove();
        });
    }
});
</script>

{include file="layouts/footer.tpl"}
