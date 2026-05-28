{* Componente card annuncio: riutilizzato in home, lista, profilo, wishlist. Mostra wishlist heart, foto, prezzo, venditore e azioni contestuali (modifica se owner, carrello se utente normale). *}
{assign var=annuncioId value=$annuncio.id_annuncio|default:0}
{assign var=annuncioOwner value=$annuncio.id_utente|default:0}
{assign var=isOwner value=$isLogged && !$isAdmin && $annuncioOwner == $userId}

<article
    class="card clickable-card annuncio-card"
    data-href="/annuncio/show/{$annuncioId}"
    role="link"
    tabindex="0">
    {if $isLogged && !$isAdmin && !$isBusiness && !$isOwner}
        {assign var=isInWishlist value=$annuncioId|in_array:$wishlistIds}
        <a
            class="wishlist-heart {if $isInWishlist}wishlist-heart-active{/if}"
            href="/wishlist/toggle/{$annuncioId}"
            title="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}"
            aria-label="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}">
            &hearts;
        </a>
    {/if}

    {if !empty($annuncio.immagine_principale)}
        <img class="annuncio-card-img" src="{$annuncio.immagine_principale}" alt="Foto annuncio">
    {/if}

    <h3>{$annuncio.titolo|default:'Annuncio'}</h3>
    <p class="muted">{$annuncio.categoria_nome|default:'Senza categoria'}</p>
    {if !empty($annuncio.descrizione)}
        <p>{$annuncio.descrizione}</p>
    {/if}
    <p class="price">&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</p>
    {if !empty($annuncio.stato_conservazione)}
        <p><strong>Conservazione:</strong> {$annuncio.stato_conservazione}</p>
    {/if}
    <p>
        <strong>Venditore:</strong>
        <a href="/utente/venditore/{$annuncioOwner}">
            <span class="seller-name-line">
                {if !empty($annuncio.venditore_business_id)}
                    {$annuncio.venditore_nome_azienda|default:'Venditore'}
                    <span class="seller-pro-badge">PRO</span>
                {else}
                    {$annuncio.venditore_username|default:'Venditore'}
                {/if}
            </span>
        </a>
    </p>

    <a class="btn" href="/annuncio/show/{$annuncioId}">Dettagli</a>

    {if $isLogged && !$isAdmin}
        {if $isOwner}
            <p class="muted u-style-024">E un tuo annuncio.</p>
            {if ($annuncio.stato|default:'') == 'attivo'}
                <a class="btn btn-secondary" href="/annuncio/edit/{$annuncioId}">Modifica</a>
            {/if}
        {elseif !$isBusiness}
            {assign var=isInCart value=$annuncioId|in_array:$carrelloIds}
            {if $isInCart}
                <span class="btn btn-secondary u-style-006">Nel carrello</span>
            {else}
                <a class="btn btn-secondary" href="/carrello/add/{$annuncioId}">Aggiungi al carrello</a>
            {/if}
        {/if}
    {/if}
</article>
