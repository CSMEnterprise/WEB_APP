{include file="layouts/header.tpl"}

<div class="paypal-page">
    <div class="card paypal-card">
        <span class="paypal-brand">PayPal</span>
        <p class="muted">Simulazione pagamento sandbox</p>

        <div class="paypal-summary">
            <h3 class="u-style-040">Riepilogo ordine</h3>

            {foreach $items as $item}
                <div class="u-style-041">
                    <div class="u-style-042">
                        <strong class="u-style-004">{$item.titolo|default:''}</strong>
                        <p class="muted u-style-043">
                            {if !empty($item.venditore_business_id)}
                                {$item.venditore_nome_azienda|default:''} <span class="seller-pro-badge">PRO</span>
                            {else}
                                {$item.venditore_username|default:''}
                            {/if}
                        </p>
                    </div>
                    <strong class="u-style-044">&euro; {$item.prezzo|default:0|number_format:2:",":"."}</strong>
                </div>
            {/foreach}

            <div class="u-style-045">
                <strong>Totale</strong>
                <span class="price u-style-046">&euro; {$totale|default:0|number_format:2:",":"."}</span>
            </div>

            <div class="u-style-047">
                <strong>Spedizione a:</strong><br>
                {$indirizzoSpedizione.via|default:''} {$indirizzoSpedizione.numero|default:''}, {$indirizzoSpedizione.cap|default:''} {$indirizzoSpedizione.citta|default:''}{if !empty($indirizzoSpedizione.provincia)} ({$indirizzoSpedizione.provincia}){/if}, {$indirizzoSpedizione.paese|default:'Italia'}
            </div>
        </div>

        <form method="post" action="index.php?route=pagamento-conferma-carrello" class="paypal-actions">
            {foreach $items as $item}
                <input type="hidden" name="id_annunci[]" value="{$item.id_annuncio|default:0}">
            {/foreach}
            <input type="hidden" name="id_indirizzo" value="{$indirizzoSpedizione.id_indirizzo|default:0}">
            <input type="hidden" name="paypal_transaction_id" value="{$paypalTransactionId|default:''}">

            <button type="submit" class="btn paypal-confirm-btn">
                Conferma pagamento - &euro; {$totale|default:0|number_format:2:",":"."}
            </button>
            <a class="btn btn-secondary" href="index.php?route=paypal-cancel">Annulla</a>
        </form>
    </div>
</div>

{include file="layouts/footer.tpl"}
