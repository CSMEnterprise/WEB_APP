{* Lista carrello: mostra gli articoli aggiunti con immagine, prezzo e pulsante rimozione. *}
{include file="layouts/header.tpl"}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">Carrello</span>
</nav>

<div class="pg-head cr-head">
    <div>
        <h1 class="pg-h1">Il tuo carrello</h1>
        <p class="pg-sub">{if !empty($carrello)}{$carrello|count_items} articol{if $carrello|count_items == 1}o{else}i{/if} pronti per il checkout{else}Il carrello e vuoto{/if}</p>
    </div>
    <div class="cr-steps">
        <span class="cr-step is-active"><span class="cr-step-num">1</span> Carrello</span>
        <span class="cr-step-line"></span>
        <span class="cr-step"><span class="cr-step-num">2</span> Checkout</span>
        <span class="cr-step-line"></span>
        <span class="cr-step"><span class="cr-step-num">3</span> Conferma</span>
    </div>
</div>

{if !empty($annunciRimossi)}
    <div class="pg-alert" data-tone="gold">Alcuni prodotti non disponibili sono stati rimossi dal carrello.</div>
{/if}

{if !empty($carrello)}
    <div class="cr-layout">
        <section class="cr-items">
            {foreach $carrello as $item}
                {assign var=itemOwner value=$item.venditore_user_id|default:0}
                {if empty($itemOwner)}{assign var=itemOwner value=$item.id_utente|default:0}{/if}
                {assign var=isPurchasable value=($item.stato|default:'') == 'attivo' && $itemOwner != $userId}
                <article class="cr-item">
                    <a class="cr-item-img" href="/annuncio/show/{$item.id_annuncio|default:0}">
                        {if !empty($item.immagine_principale)}
                            <img src="{$item.immagine_principale}" alt="Foto annuncio">
                        {else}
                            <span>Nessuna foto</span>
                        {/if}
                    </a>
                    <div class="cr-item-body">
                        <div class="annuncio-card-meta">
                            <span class="annuncio-card-cat">{$item.categoria_nome|default:'Senza categoria'}</span>
                            <span>&middot; {$item.stato_conservazione|default:'Non specificato'}</span>
                        </div>
                        <h3 class="cr-item-title"><a href="/annuncio/show/{$item.id_annuncio|default:0}">{$item.titolo|default:'Annuncio'}</a></h3>
                        {if !$isPurchasable}<span class="pg-pill" data-tone="danger">Non acquistabile</span>{/if}
                    </div>
                    <div class="cr-item-controls">
                        <span class="cr-item-price">&euro; {$item.prezzo|default:0|number_format:2:",":"."}</span>
                        <form class="u-post-form" method="post" action="/carrello/remove">
                            <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                            <input type="hidden" name="id_annuncio" value="{$item.id_annuncio|default:0}">
                            <button class="cr-item-remove u-post-button" type="submit" aria-label="Rimuovi">&times;</button>
                        </form>
                    </div>
                </article>
            {/foreach}

            <div class="cr-actions-row">
                <a class="va-link" href="/home/index">Continua lo shopping</a>
                <form class="u-post-form" method="post" action="/carrello/clear">
                    <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                    <button class="va-link u-post-button" type="submit">Svuota carrello</button>
                </form>
            </div>
        </section>

        <aside class="cr-summary-wrap">
            <div class="pg-card cr-summary">
                <h2 class="pg-card-title">Riepilogo ordine</h2>
                <div class="cr-summary-row"><span>Subtotale</span><span>&euro; {$totale|default:0|number_format:2:",":"."}</span></div>
                <div class="cr-summary-row"><span>Spedizione</span><span>Calcolata al checkout</span></div>
                <hr class="pg-divider pg-divider-dashed">
                <div class="cr-summary-total"><span>Totale</span><span>&euro; {$totale|default:0|number_format:2:",":"."}</span></div>

                {if !empty($purchasableItems)}
                    <a class="btn btn-block" data-size="lg" href="/pagamento/checkout-carrello">Procedi al checkout</a>
                {else}
                    <p class="muted">Nessun articolo acquistabile nel carrello.</p>
                {/if}

                <div class="cr-summary-trust">
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Pagamento protetto</div>
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Feedback verificato</div>
                    <div><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"></path></svg> Supporto NerdVault</div>
                </div>
            </div>
        </aside>
    </div>
{else}
    <div class="pg-card cr-empty">
        <div class="cr-empty-glyph">🛒</div>
        <h2 class="pg-card-title">Carrello vuoto</h2>
        <p class="muted">Esplora migliaia di annunci da appassionati e venditori PRO.</p>
        <a class="btn" href="/home/index">Esplora annunci</a>
    </div>
{/if}

{include file="layouts/footer.tpl"}
