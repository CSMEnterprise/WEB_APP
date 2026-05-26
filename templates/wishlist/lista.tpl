{include file="layouts/header.tpl"}

<div class="u-style-007">
    <h1 class="u-style-008">Wishlist</h1>
    {if !empty($wishlist)}
        <a class="btn btn-secondary" href="index.php?route=wishlist-clear">Svuota wishlist</a>
    {/if}
</div>

{if !empty($wishlist)}
    <div class="grid">
        {foreach $wishlist as $annuncio}
            {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=[]}
        {/foreach}
    </div>
{else}
    <section class="card">
        <p>La tua wishlist e vuota.</p>
        <a class="btn" href="index.php?route=home">Esplora annunci</a>
    </section>
{/if}

{include file="layouts/footer.tpl"}
