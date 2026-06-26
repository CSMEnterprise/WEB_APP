{* Componente card annuncio in stile NerdVault Pages.html / va-card. *}
{assign var=annuncioId value=$annuncio.id_annuncio|default:0}
{assign var=annuncioOwner value=$annuncio.venditore_user_id|default:0}
{if empty($annuncioOwner)}{assign var=annuncioOwner value=$annuncio.id_utente|default:0}{/if}
{assign var=annuncioBusinessOwner value=$annuncio.id_business|default:0}
{assign var=isOwner value=$isLogged && !$isAdmin && (($annuncio.id_utente|default:0) == $userId || ($annuncioBusinessOwner > 0 && $annuncioBusinessOwner == $businessId))}
{if !empty($annuncio.venditore_business_id)}
    {assign var=sellerName value=$annuncio.venditore_nome_azienda|default:'Venditore'}
{else}
    {assign var=sellerName value=$annuncio.venditore_username|default:'Venditore'}
{/if}
{assign var=sellerFeedbackCount value=$annuncio.venditore_feedback_count|default:0}
{assign var=sellerRating value=$annuncio.venditore_media_feedback|default:0}
{assign var=sellerRatingLabel value=$sellerRating|number_format:1:",":"."}
{assign var=sellerPropic value=$annuncio.venditore_propic|default:''}

<article
    class="annuncio-card va-card clickable-card"
    data-href="/annuncio/show/{$annuncioId}"
    role="link"
    tabindex="0">

    <div class="annuncio-card-media va-card-media">
        {if $isLogged && !$isAdmin && !$isBusiness && !$isOwner}
            {assign var=isInWishlist value=$annuncioId|in_array:$wishlistIds}
            <form class="u-post-form" method="post" action="/wishlist/toggle">
                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                <input type="hidden" name="id_annuncio" value="{$annuncioId}">
                <button
                    class="wishlist-heart nv-heart u-post-button {if $isInWishlist}wishlist-heart-active{/if}"
                    type="submit"
                    title="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}"
                    aria-label="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}">
                    &hearts;
                </button>
            </form>
        {/if}

        {if !empty($annuncio.immagine_principale)}
            <img class="annuncio-card-img va-card-img" src="{$annuncio.immagine_principale}" alt="Foto annuncio">
        {else}
            <div class="annuncio-card-ph va-card-ph">Nessuna foto</div>
        {/if}
    </div>

    <div class="annuncio-card-body va-card-body">
        <div class="annuncio-card-meta va-card-meta">
            <span class="annuncio-card-cat va-card-cat">{$annuncio.categoria_nome|default:'Senza categoria'}</span>
            {if !empty($annuncio.stato_conservazione)}
                <span>&middot; {$annuncio.stato_conservazione}</span>
            {/if}
        </div>

        <h3 class="va-card-title">{$annuncio.titolo|default:'Annuncio'}</h3>

        <div class="annuncio-card-seller va-card-seller">
            <span class="annuncio-card-seller-av va-card-seller-av">
                {if !empty($sellerPropic)}
                    <img class="seller-avatar-img" src="{$sellerPropic}" alt="">
                {else}
                    {$sellerName|truncate:1:"":true|upper}
                {/if}
            </span>
            <a class="va-card-seller-name" href="/utente/venditore/{$annuncioOwner}">{$sellerName}</a>
            {if !empty($annuncio.venditore_business_id)}
                <span class="seller-pro-badge nv-pro-badge">PRO</span>
            {/if}
            <span class="annuncio-card-rating va-card-rating" aria-label="{if $sellerFeedbackCount > 0}Rating venditore {$sellerRatingLabel} su 5{else}Venditore senza recensioni{/if}">
                {if $sellerFeedbackCount > 0}
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
                    {$sellerRatingLabel}
                {else}
                    Nuovo
                {/if}
            </span>
        </div>

        <div class="annuncio-card-foot va-card-foot">
            <p class="price va-card-price">&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</p>

            {if $isLogged && !$isAdmin}
                {if $isOwner}
                    {if ($annuncio.stato|default:'') == 'attivo'}
                        <a class="btn btn-secondary" data-size="sm" href="/annuncio/edit/{$annuncioId}">Modifica</a>
                    {/if}
                {elseif !$isBusiness}
                    {assign var=isInCart value=$annuncioId|in_array:$carrelloIds}
                    {if $isInCart}
                        <span class="btn u-style-006" data-size="sm" data-variant="dark">Nel carrello</span>
                    {else}
                        <form class="u-post-form-flex" method="post" action="/carrello/add">
                            <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                            <input type="hidden" name="id_annuncio" value="{$annuncioId}">
                            <button class="btn" data-size="sm" type="submit">Al carrello</button>
                        </form>
                    {/if}
                {/if}
            {/if}
        </div>

        {if $isOwner}
            <p class="annuncio-card-owner-note">E un tuo annuncio.</p>
        {/if}
    </div>
</article>
