{* Checkout acquisto singolo in stile NerdVault Pages.html: riepilogo, indirizzo e metodo PayPal simulato. *}
{include file="layouts/header.tpl"}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <a href="/carrello/list">Carrello</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">Checkout</span>
</nav>

<div class="pg-head cr-head">
    <div>
        <h1 class="pg-h1">Checkout</h1>
        <p class="pg-sub">Conferma indirizzo e pagamento protetto per completare l'acquisto.</p>
    </div>
    <div class="cr-steps">
        <span class="cr-step is-active"><span class="cr-step-num">1</span> Carrello</span>
        <span class="cr-step-line"></span>
        <span class="cr-step is-active"><span class="cr-step-num">2</span> Checkout</span>
        <span class="cr-step-line"></span>
        <span class="cr-step"><span class="cr-step-num">3</span> Conferma</span>
    </div>
</div>

{if !empty($annuncio)}
    <section class="cr-layout">
        <div class="cr-checkout">
            <section class="pg-card co-card">
                <h2 class="pg-card-title">Indirizzo di spedizione</h2>
                <p class="pg-card-sub">Scegli dove ricevere l'articolo. Puoi gestire gli indirizzi dal profilo.</p>

                {if !empty($indirizziUtente)}
                    <form method="post" action="/pagamento/paypal" class="co-form">
                        <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:0}">

                        <div class="co-address-list">
                            {foreach $indirizziUtente as $indirizzo}
                                <label class="co-address {if !empty($indirizzo.predefinito)}is-default{/if}">
                                    <input
                                        type="radio"
                                        name="id_indirizzo"
                                        value="{$indirizzo.id_indirizzo|default:0}"
                                        {if !empty($indirizzo.predefinito)}checked{/if}
                                        required>
                                    <span class="co-radio" aria-hidden="true"><span></span></span>
                                    <span class="co-address-body">
                                        <strong>{$indirizzo.via|default:''} {$indirizzo.numero|default:''}</strong>
                                        <span>{$indirizzo.cap|default:''} {$indirizzo.citta|default:''}{if !empty($indirizzo.provincia)} ({$indirizzo.provincia}){/if}, {$indirizzo.paese|default:'Italia'}</span>
                                        {if !empty($indirizzo.predefinito)}<em>Predefinito</em>{/if}
                                    </span>
                                </label>
                            {/foreach}
                        </div>

                        <section class="pg-card co-card co-pay-card">
                            <h2 class="pg-card-title">Metodo di pagamento</h2>
                            <p class="pg-card-sub">Pagamento simulato PayPal, protetto da NerdVault fino alla consegna.</p>
                            <div class="cr-pay-options">
                                <div class="cr-pay-opt is-on">
                                    <span class="cr-pay-radio"><span class="cr-pay-radio-dot"></span></span>
                                    <span class="cr-pay-body"><strong>PayPal</strong><span>Accesso alla schermata di pagamento simulata</span></span>
                                </div>
                                <div class="cr-pay-opt is-disabled" aria-disabled="true">
                                    <span class="cr-pay-radio"></span>
                                    <span class="cr-pay-body"><strong>Carta</strong><span>Visa, Mastercard, Amex - presto disponibile</span></span>
                                </div>
                                <div class="cr-pay-opt is-disabled" aria-disabled="true">
                                    <span class="cr-pay-radio"></span>
                                    <span class="cr-pay-body"><strong>Bonifico SEPA</strong><span>Trasferimento bancario - presto disponibile</span></span>
                                </div>
                            </div>
                        </section>

                        <button class="btn co-mobile-submit" data-size="lg" type="submit">Continua con PayPal</button>
                    </form>
                {else}
                    <div class="pg-alert" data-tone="danger">Aggiungi un indirizzo di spedizione prima di procedere al pagamento.</div>
                    <a class="btn" href="/utente/profilo">Vai al profilo</a>
                {/if}
            </section>
        </div>

        <aside class="cr-summary-wrap">
            <div class="pg-card cr-summary co-summary">
                <h2 class="pg-card-title">Riepilogo ordine</h2>
                <article class="co-line">
                    <a class="co-line-img" href="/annuncio/show/{$annuncio.id_annuncio|default:0}">
                        {if !empty($annuncio.immagine_principale)}
                            <img src="{$annuncio.immagine_principale}" alt="Foto annuncio">
                        {else}
                            <span>Nessuna foto</span>
                        {/if}
                    </a>
                    <div class="co-line-body">
                        <strong>{$annuncio.titolo|default:'Annuncio'}</strong>
                        <span>
                            {if !empty($annuncio.venditore_business_id)}
                                {$annuncio.venditore_nome_azienda|default:'Venditore'} PRO
                            {else}
                                {$annuncio.venditore_username|default:'Venditore'}
                            {/if}
                        </span>
                    </div>
                    <span class="co-line-price">&euro; {$totale|default:0|number_format:2:",":"."}</span>
                </article>
                <div class="cr-summary-row"><span>Subtotale</span><span>&euro; {$totale|default:0|number_format:2:",":"."}</span></div>
                <div class="cr-summary-row"><span>Spedizione</span><span>Calcolata dopo il pagamento</span></div>
                <hr class="pg-divider pg-divider-dashed">
                <div class="cr-summary-total"><span>Totale</span><span>&euro; {$totale|default:0|number_format:2:",":"."}</span></div>
                {if !empty($indirizziUtente)}
                    <button class="btn btn-block co-submit-proxy" data-size="lg" type="button" data-submit-checkout>Continua con PayPal</button>
                {/if}
                <div class="cr-summary-trust">
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Pagamento protetto</div>
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Reso entro 14 giorni</div>
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Assistenza NerdVault</div>
                </div>
            </div>
        </aside>
    </section>

    <script>
    document.querySelectorAll('[data-submit-checkout]').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.querySelector('.co-form');
            if (form) form.requestSubmit();
        });
    });
    </script>
{else}
    <div class="pg-alert" data-tone="danger">Pagamento non disponibile.</div>
{/if}

{include file="layouts/footer.tpl"}
