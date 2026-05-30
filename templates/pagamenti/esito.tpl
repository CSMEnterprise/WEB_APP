{* Esito pagamento in stile NerdVault Pages.html. *}
{include file="layouts/header.tpl"}

<main class="pg-narrow">
    {if ($status|default:'') == 'ok'}
        <section class="es-card">
            <div class="es-badge es-badge-ok">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
            </div>
            <h1 class="es-title">Pagamento completato</h1>
            <p class="es-sub">
                Grazie! Il tuo ordine e confermato.
                {if $numeroPagamenti|default:0 > 1}Sono stati completati <strong>{$numeroPagamenti}</strong> pagamenti.{/if}
                Riceverai aggiornamenti appena il venditore spedira.
            </p>

            <div class="es-summary">
                <div class="es-summary-head">
                    <span>Stato ordine</span>
                    <span class="pg-pill" data-tone="success">Pagato</span>
                </div>
                <div class="es-row"><span>Pagamento</span><span>Completato</span></div>
                <div class="es-row"><span>Protezione NerdVault</span><span>Attiva</span></div>
                <div class="es-row es-row-total"><span>Prossimo passo</span><span>Spedizione</span></div>
            </div>

            <div class="es-next">
                <div class="es-next-step">
                    <span class="es-next-num">1</span>
                    <div><strong>Pagamento in garanzia</strong><span>Trattenuto da NerdVault fino alla consegna.</span></div>
                </div>
                <div class="es-next-step">
                    <span class="es-next-num">2</span>
                    <div><strong>Spedizione</strong><span>Tracking via email appena disponibile.</span></div>
                </div>
                <div class="es-next-step">
                    <span class="es-next-num">3</span>
                    <div><strong>Feedback</strong><span>Lascia una recensione dopo la transazione.</span></div>
                </div>
            </div>

            <div class="es-actions">
                <a class="btn" data-size="lg" href="/feedback/create/{$idPagamento|default:0}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
                    Lascia un feedback
                </a>
                <a class="btn btn-secondary" data-size="lg" href="/home/index">Torna agli annunci</a>
            </div>
        </section>
    {else}
        <section class="es-card">
            <div class="es-badge es-badge-fail">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </div>
            <h1 class="es-title">Pagamento non completato</h1>
            <p class="es-sub">Qualcosa e andato storto e l'ordine non e stato addebitato. Puoi tornare agli annunci e riprovare.</p>

            <div class="pg-alert" data-tone="danger">
                <span>Transazione non completata. Nessun importo e stato prelevato.</span>
            </div>

            <div class="es-actions">
                <a class="btn" data-size="lg" href="/carrello/list">Torna al carrello</a>
                <a class="btn btn-secondary" data-size="lg" href="/home/index">Torna agli annunci</a>
            </div>
        </section>
    {/if}
</main>

{include file="layouts/footer.tpl"}
