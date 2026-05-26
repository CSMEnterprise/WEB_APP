{include file="layouts/header.tpl"}

{if !empty($annuncio)}
    {assign var=annuncioId value=$annuncio.id_annuncio|default:0}
    {assign var=annuncioOwner value=$annuncio.id_utente|default:0}
    {assign var=isOwner value=$isLogged && !$isAdmin && $annuncioOwner == $userId}
    {assign var=canUseWishlist value=$isLogged && !$isAdmin && !$isBusiness && !$isOwner}
    {assign var=isInWishlist value=$annuncioId|in_array:$wishlistIds}
    {assign var=isInCart value=$annuncioId|in_array:$carrelloIds}
    {assign var=stelleVenditore value=$mediaVenditore|default:0|round}

    <article class="card annuncio-card">
        {if $canUseWishlist}
            <a
                class="wishlist-heart {if $isInWishlist}wishlist-heart-active{/if}"
                href="index.php?route=wishlist-toggle&id={$annuncioId}"
                title="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}"
                aria-label="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}">
                &hearts;
            </a>
        {/if}

        <h1>{$annuncio.titolo|default:''}</h1>

        {if !empty($annuncio.immagini)}
            <div class="annuncio-detail-gallery" data-gallery>
                <div class="annuncio-gallery-main">
                    {if $annuncio.immagini|count_items > 1}
                        <button class="annuncio-gallery-nav annuncio-gallery-prev" type="button" data-gallery-prev aria-label="Foto precedente">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                        </button>
                    {/if}

                    <img src="{$annuncio.immagini.0.url|default:''}" alt="Foto annuncio" data-gallery-main>

                    {if $annuncio.immagini|count_items > 1}
                        <button class="annuncio-gallery-nav annuncio-gallery-next" type="button" data-gallery-next aria-label="Foto successiva">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                        </button>
                    {/if}
                </div>

                {if $annuncio.immagini|count_items > 1}
                    <div class="annuncio-gallery-thumbs" aria-label="Foto annuncio">
                        {foreach $annuncio.immagini as $immagine}
                            <button
                                class="annuncio-gallery-thumb {if $immagine@first}is-active{/if}"
                                type="button"
                                data-gallery-thumb
                                data-gallery-src="{$immagine.url|default:''}"
                                aria-label="Mostra foto {$immagine@iteration}">
                                <img src="{$immagine.url|default:''}" alt="">
                            </button>
                        {/foreach}
                    </div>
                {/if}
            </div>
        {/if}

        <p class="muted">{$annuncio.categoria_nome|default:''}</p>
        <p>{$annuncio.descrizione|default:''|nl2br_e nofilter}</p>
        <p class="price">&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</p>
        <p><strong>Conservazione:</strong> {$annuncio.stato_conservazione|default:'Non specificato'}</p>
        <p><strong>Stato vendita:</strong> {$annuncio.stato|default:''|ucfirst}</p>

        <p>
            <strong>Venditore:</strong>
            <a href="index.php?route=venditore&id={$annuncioOwner}">
                <span class="seller-name-line">
                    {if !empty($annuncio.venditore_business_id)}
                        {$annuncio.venditore_nome_azienda|default:'Venditore'} <span class="seller-pro-badge">PRO</span>
                    {else}
                        {$annuncio.venditore_username|default:'Venditore'}
                    {/if}
                </span>
            </a>
            {if $feedbackVenditore|count_items > 0}
                <span class="u-rating-inline" title="{$mediaVenditore|number_format:1:",":"."} su 5">
                    {include file="components/stars.tpl" value=$stelleVenditore}
                    <strong class="u-style-003">{$mediaVenditore|number_format:1:",":"."}</strong>
                    <span class="muted u-style-004">({$feedbackVenditore|count_items})</span>
                </span>
            {else}
                <span class="muted u-style-005">Nessuna recensione</span>
            {/if}
            <a class="btn btn-secondary u-small-inline-link" href="index.php?route=venditore&id={$annuncioOwner}">Vedi profilo</a>
        </p>

        {if $isLogged}
            {if $isAdmin}
                <div class="alert alert-success">Accesso admin: carrello, wishlist e acquisto sono disattivati.</div>
            {elseif $isBusiness && !$isOwner}
                <div class="alert alert-success">Account business: puoi vendere prodotti, ma carrello, wishlist e acquisto sono disattivati.</div>
                <a class="btn btn-secondary" href="index.php?route=segnalazione-create&id_annuncio={$annuncioId}">Segnala</a>
            {elseif $isOwner}
                <div class="alert alert-success">Questo e un tuo annuncio: carrello e acquisto sono disattivati.</div>
                {if ($annuncio.stato|default:'') == 'attivo'}
                    <a class="btn" href="index.php?route=annuncio-edit&id={$annuncioId}">Modifica</a>
                    <a class="btn btn-danger" href="index.php?route=annuncio-delete&id={$annuncioId}">Elimina</a>
                {/if}
            {else}
                {if $isInCart}
                    <span class="btn btn-secondary u-style-006">Nel carrello</span>
                {else}
                    <a class="btn" href="index.php?route=carrello-add&id={$annuncioId}">Aggiungi al carrello</a>
                {/if}
                <a class="btn btn-secondary" href="index.php?route=checkout&id={$annuncioId}">Acquista</a>
                <a class="btn btn-secondary" href="index.php?route=segnalazione-create&id_annuncio={$annuncioId}">Segnala</a>
            {/if}
        {/if}
    </article>

    <script>
    document.querySelectorAll('[data-gallery]').forEach(function (gallery) {
        const mainImage = gallery.querySelector('[data-gallery-main]');
        const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
        const prevButton = gallery.querySelector('[data-gallery-prev]');
        const nextButton = gallery.querySelector('[data-gallery-next]');
        let activeIndex = 0;

        if (!mainImage || thumbs.length === 0) return;

        function showImage(index) {
            activeIndex = (index + thumbs.length) % thumbs.length;
            const activeThumb = thumbs[activeIndex];
            const nextSrc = activeThumb.getAttribute('data-gallery-src');

            if (nextSrc) mainImage.src = nextSrc;

            thumbs.forEach(function (thumb, thumbIndex) {
                thumb.classList.toggle('is-active', thumbIndex === activeIndex);
            });
        }

        thumbs.forEach(function (thumb, index) {
            thumb.addEventListener('click', function () { showImage(index); });
        });

        if (prevButton) prevButton.addEventListener('click', function () { showImage(activeIndex - 1); });
        if (nextButton) nextButton.addEventListener('click', function () { showImage(activeIndex + 1); });
    });
    </script>
{else}
    <div class="alert alert-error">Annuncio non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
