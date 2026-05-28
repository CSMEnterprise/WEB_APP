{* Lista carrello: mostra gli articoli aggiunti con immagine, prezzo e pulsante rimozione. Gli articoli non acquistabili (venduti o propri) mostrano un avviso. Il checkout è abilitato solo se ci sono articoli acquistabili. *}
{include file="layouts/header.tpl"}

<div class="u-style-007">
    <h1 class="u-style-008">Carrello</h1>
    {if !empty($carrello)}
        <a class="btn btn-secondary" href="/carrello/clear">Svuota carrello</a>
    {/if}
</div>

{if !empty($annunciRimossi)}
    <div class="alert alert-success">Alcuni prodotti non disponibili sono stati rimossi dal carrello.</div>
{/if}

{if !empty($carrello)}
    <div class="grid">
        {foreach $carrello as $item}
            {assign var=isPurchasable value=($item.stato|default:'') == 'attivo' && ($item.id_utente|default:0) != $userId}
            <article class="card">
                {if !empty($item.immagine_principale)}
                    <img class="annuncio-card-img" src="{$item.immagine_principale}" alt="Foto annuncio">
                {/if}
                <h2>{$item.titolo|default:'Annuncio'}</h2>
                <p class="muted">{$item.categoria_nome|default:'Senza categoria'}</p>
                <p class="price">&euro; {$item.prezzo|default:0|number_format:2:",":"."}</p>
                {if !$isPurchasable}
                    <div class="alert alert-error">Questo prodotto non e acquistabile.</div>
                {/if}
                <a class="btn" href="/annuncio/show/{$item.id_annuncio|default:0}">Dettagli</a>
                <a class="btn btn-secondary" href="/carrello/remove/{$item.id_annuncio|default:0}">Rimuovi</a>
            </article>
        {/foreach}
    </div>

    <section class="card">
        <h2>Totale</h2>
        <p class="price">&euro; {$totale|default:0|number_format:2:",":"."}</p>
        {if !empty($purchasableItems)}
            <a class="btn" href="/pagamento/checkout-carrello">Procedi al checkout</a>
        {else}
            <p class="muted">Nessun articolo acquistabile nel carrello.</p>
        {/if}
    </section>
{else}
    <section class="card">
        <p>Il carrello e vuoto.</p>
        <a class="btn" href="/home/index">Esplora annunci</a>
    </section>
{/if}

{include file="layouts/footer.tpl"}
