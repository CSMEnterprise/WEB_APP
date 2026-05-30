{* Conferma PayPal simulata per acquisto singolo, coerente con NerdVault Pages.html. *}
{include file="layouts/header.tpl"}

<main class="pg-narrow">
    <nav class="pg-breadcrumb">
        <a href="/home/index">Home</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="current">Conferma pagamento</span>
    </nav>

    <section class="pp-card">
        <div class="pp-brand">PayPal</div>
        <h1 class="pp-title">Pagamento simulato</h1>
        <p class="pp-sub">Controlla riepilogo e indirizzo prima di confermare. Nessun pagamento reale verra effettuato.</p>

        {if !empty($annuncio)}
            <div class="es-summary pp-summary">
                <div class="es-summary-head">
                    <span>Riepilogo ordine</span>
                    <span class="pg-pill" data-tone="gold">Sandbox</span>
                </div>
                <article class="es-line">
                    <div class="es-line-img">
                        {if !empty($annuncio.immagine_principale)}
                            <img src="{$annuncio.immagine_principale}" alt="Foto annuncio">
                        {else}
                            <span>NV</span>
                        {/if}
                    </div>
                    <div class="es-line-body">
                        <strong>{$annuncio.titolo|default:'Annuncio'}</strong>
                        <span>
                            {if !empty($annuncio.venditore_business_id)}
                                {$annuncio.venditore_nome_azienda|default:'Venditore'} PRO
                            {else}
                                {$annuncio.venditore_username|default:'Venditore'}
                            {/if}
                        </span>
                    </div>
                    <span class="es-line-price">&euro; {$totale|default:0|number_format:2:",":"."}</span>
                </article>
                <div class="pg-divider pg-divider-dashed"></div>
                <div class="es-row"><span>Spedizione a</span><span>{$indirizzoSpedizione.citta|default:'Italia'}</span></div>
                <p class="pp-address">
                    {$indirizzoSpedizione.via|default:''} {$indirizzoSpedizione.numero|default:''}, {$indirizzoSpedizione.cap|default:''} {$indirizzoSpedizione.citta|default:''}{if !empty($indirizzoSpedizione.provincia)} ({$indirizzoSpedizione.provincia}){/if}, {$indirizzoSpedizione.paese|default:'Italia'}
                </p>
                <div class="es-row es-row-total"><span>Totale</span><span>&euro; {$totale|default:0|number_format:2:",":"."}</span></div>
            </div>

            <form method="post" action="/pagamento/conferma" class="pp-actions">
                <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:0}">
                <input type="hidden" name="id_indirizzo" value="{$indirizzoSpedizione.id_indirizzo|default:0}">
                <input type="hidden" name="paypal_transaction_id" value="{$paypalTransactionId|default:''}">
                <button class="btn" data-size="lg" type="submit">Conferma pagamento - &euro; {$totale|default:0|number_format:2:",":"."}</button>
                <a class="btn btn-secondary" data-size="lg" href="/pagamento/cancel">Annulla</a>
            </form>
        {else}
            <div class="pg-alert" data-tone="danger">Pagamento non disponibile.</div>
            <a class="btn" href="/carrello/list">Torna al carrello</a>
        {/if}
    </section>
</main>

{include file="layouts/footer.tpl"}
