{* Checkout carrello: lista di tutti gli articoli selezionati, totale, selezione indirizzo di spedizione e conferma per il pagamento multiplo PayPal simulato. *}
{include file="layouts/header.tpl"}

<h1>Checkout</h1>

<section class="cart-layout">
    <div>
        {foreach $items as $item}
            <article class="card u-style-036">
                {if !empty($item.immagine_principale)}
                    <img class="u-checkout-thumb" src="{$item.immagine_principale}" alt="Foto">
                {/if}
                <div>
                    <strong>{$item.titolo|default:''}</strong>
                    <p class="price u-style-037">&euro; {$item.prezzo|default:0|number_format:2:",":"."}</p>
                    <p class="muted u-style-019">
                        Venditore:
                        {if !empty($item.venditore_business_id)}
                            {$item.venditore_nome_azienda|default:''} <span class="seller-pro-badge">PRO</span>
                        {else}
                            {$item.venditore_username|default:''}
                        {/if}
                    </p>
                </div>
            </article>
        {/foreach}
    </div>

    <aside class="card cart-summary">
        <h2>Totale</h2>
        <p class="price">&euro; {$totale|default:0|number_format:2:",":"."}</p>
        <p class="muted">{$items|count_items} {if $items|count_items == 1}articolo{else}articoli{/if}</p>

        <h2 class="u-style-038">Indirizzo di spedizione</h2>

        {if !empty($indirizziUtente)}
            <form method="get" action="index.php" class="cart-summary-actions">
                <input type="hidden" name="route" value="paypal-placeholder-carrello">

                {foreach $indirizziUtente as $indirizzo}
                    <label class="u-style-039">
                        <input
                            type="radio"
                            name="id_indirizzo"
                            value="{$indirizzo.id_indirizzo|default:0}"
                            {if !empty($indirizzo.predefinito)}checked{/if}
                            required
                            class="u-radio-compact">
                        <span>
                            {$indirizzo.via|default:''} {$indirizzo.numero|default:''}, {$indirizzo.cap|default:''} {$indirizzo.citta|default:''}{if !empty($indirizzo.provincia)} ({$indirizzo.provincia}){/if}, {$indirizzo.paese|default:'Italia'}
                            {if !empty($indirizzo.predefinito)}<span class="seller-pro-badge u-style-035">Predefinito</span>{/if}
                        </span>
                    </label>
                {/foreach}

                <button class="btn" type="submit">Continua con PayPal</button>
            </form>
        {else}
            <div class="alert alert-error">Aggiungi un indirizzo di spedizione prima di procedere al pagamento.</div>
            <a class="btn" href="index.php?route=profilo">Vai al profilo</a>
        {/if}
    </aside>
</section>

{include file="layouts/footer.tpl"}
