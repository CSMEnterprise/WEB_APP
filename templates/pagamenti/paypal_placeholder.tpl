{include file="layouts/header.tpl"}

<section class="paypal-page">
    <div class="paypal-card">
        <div class="paypal-brand">PayPal</div>
        <h1>Pagamento simulato</h1>
        <p class="muted">Questa schermata e un placeholder per simulare il pagamento PayPal. Nessun pagamento reale verra effettuato.</p>

        {if !empty($annuncio)}
            <div class="paypal-summary">
                <h2>Riepilogo ordine</h2>

                {if !empty($annuncio.immagine_principale)}
                    <img class="paypal-product-img" src="{$annuncio.immagine_principale}" alt="Foto annuncio">
                {/if}

                <p><strong>Annuncio:</strong> {$annuncio.titolo|default:''}</p>
                <p>
                    <strong>Venditore:</strong>
                    <a href="index.php?route=venditore&id={$annuncio.id_utente|default:0}">
                        {if !empty($annuncio.venditore_business_id)}
                            {$annuncio.venditore_nome_azienda|default:''} <span class="seller-pro-badge">PRO</span>
                        {else}
                            {$annuncio.venditore_username|default:''}
                        {/if}
                    </a>
                </p>
                <p class="price">Totale: &euro; {$totale|default:0|number_format:2:",":"."}</p>

                {if !empty($indirizzoSpedizione)}
                    <p><strong>Spedizione:</strong> {$indirizzoSpedizione.via|default:''} {$indirizzoSpedizione.numero|default:''}, {$indirizzoSpedizione.cap|default:''} {$indirizzoSpedizione.citta|default:''}{if !empty($indirizzoSpedizione.provincia)} ({$indirizzoSpedizione.provincia}){/if}, {$indirizzoSpedizione.paese|default:'Italia'}</p>
                {/if}
            </div>

            <form method="post" action="index.php?route=pagamento-conferma" class="paypal-actions">
                <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:0}">
                <input type="hidden" name="id_indirizzo" value="{$indirizzoSpedizione.id_indirizzo|default:0}">
                <input type="hidden" name="paypal_transaction_id" value="{$paypalTransactionId|default:''}">
                <button class="btn paypal-confirm-btn" type="submit">Paga con PayPal simulato</button>
                <a class="btn btn-secondary" href="index.php?route=paypal-cancel">Annulla</a>
            </form>
        {else}
            <div class="alert alert-error">Pagamento non disponibile.</div>
            <a class="btn" href="index.php?route=carrello">Torna al carrello</a>
        {/if}
    </div>
</section>

{include file="layouts/footer.tpl"}
