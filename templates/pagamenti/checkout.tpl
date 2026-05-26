{include file="layouts/header.tpl"}

<h1>Checkout</h1>

{if !empty($annuncio)}
    <section class="cart-layout">
        <div class="card">
            <h2>{$annuncio.titolo|default:''}</h2>
            <p>{$annuncio.descrizione|default:''}</p>
            <p>
                <strong>Venditore:</strong>
                <a href="index.php?route=venditore&id={$annuncio.id_utente|default:0}">
                    <span class="seller-name-line">
                        {if !empty($annuncio.venditore_business_id)}
                            {$annuncio.venditore_nome_azienda|default:''} <span class="seller-pro-badge">PRO</span>
                        {else}
                            {$annuncio.venditore_username|default:''}
                        {/if}
                    </span>
                </a>
            </p>
            <p class="price">Totale: &euro; {$totale|default:0|number_format:2:",":"."}</p>
        </div>

        <aside class="card cart-summary">
            <h2>Indirizzo di spedizione</h2>

            {if !empty($indirizziUtente)}
                <form method="post" action="index.php?route=paypal-placeholder" class="cart-summary-actions">
                    <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:0}">

                    {foreach $indirizziUtente as $indirizzo}
                        <label class="u-style-034">
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
{else}
    <div class="alert alert-error">Pagamento non disponibile.</div>
{/if}

{include file="layouts/footer.tpl"}
