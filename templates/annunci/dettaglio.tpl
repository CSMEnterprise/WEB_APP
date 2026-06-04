{* Dettaglio annuncio: galleria foto con navigazione, info venditore con rating, azioni contestuali in base al ruolo. *}
{include file="layouts/header.tpl"}

{if !empty($annuncio)}
    {assign var=annuncioId value=$annuncio.id_annuncio|default:0}
    {assign var=annuncioOwner value=$annuncio.venditore_user_id|default:0}
    {if empty($annuncioOwner)}{assign var=annuncioOwner value=$annuncio.id_utente|default:0}{/if}
    {assign var=annuncioBusinessOwner value=$annuncio.id_business|default:0}
    {assign var=isOwner value=$isLogged && !$isAdmin && (($annuncio.id_utente|default:0) == $userId || ($annuncioBusinessOwner > 0 && $annuncioBusinessOwner == $businessId))}
    {assign var=canUseWishlist value=$isLogged && !$isAdmin && !$isBusiness && !$isOwner}
    {assign var=isInWishlist value=$annuncioId|in_array:$wishlistIds}
    {assign var=isInCart value=$annuncioId|in_array:$carrelloIds}
    {assign var=stelleVenditore value=$mediaVenditore|default:0|round}
    {assign var=feedbackCount value=$feedbackVenditore|count_items}
    {if !empty($annuncio.venditore_business_id)}
        {assign var=sellerName value=$annuncio.venditore_nome_azienda|default:'Venditore'}
    {else}
        {assign var=sellerName value=$annuncio.venditore_username|default:'Venditore'}
    {/if}

    <nav class="pg-breadcrumb">
        <a href="/home/index">Home</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        {if !empty($annuncio.categoria_nome)}
            <a href="/annuncio/list">{$annuncio.categoria_nome}</a>
        {else}
            <a href="/annuncio/list">Annunci</a>
        {/if}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="current">{$annuncio.titolo|default:''}</span>
    </nav>

    <div class="dt-layout">
        <section class="dt-gallery">
            {if !empty($annuncio.immagini)}
                <div class="annuncio-detail-gallery" data-gallery>
                    <div class="annuncio-gallery-main dt-gallery-main">
                        {if $annuncio.immagini|count_items > 1}
                            <button class="annuncio-gallery-nav annuncio-gallery-prev dt-gnav dt-gnav-prev" type="button" data-gallery-prev aria-label="Foto precedente">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                            </button>
                        {/if}

                        <img src="{$annuncio.immagini.0.url|default:''}" alt="Foto annuncio" data-gallery-main>

                        {if $annuncio.immagini|count_items > 1}
                            <button class="annuncio-gallery-nav annuncio-gallery-next dt-gnav dt-gnav-next" type="button" data-gallery-next aria-label="Foto successiva">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                            </button>
                            <span class="dt-gallery-counter">1 / {$annuncio.immagini|count_items}</span>
                        {/if}
                    </div>

                    {if $annuncio.immagini|count_items > 1}
                        <div class="annuncio-gallery-thumbs dt-thumbs" aria-label="Foto annuncio">
                            {foreach $annuncio.immagini as $immagine}
                                <button class="annuncio-gallery-thumb dt-thumb {if $immagine@first}is-active{/if}" type="button" data-gallery-thumb data-gallery-src="{$immagine.url|default:''}" aria-label="Mostra foto {$immagine@iteration}">
                                    <img src="{$immagine.url|default:''}" alt="">
                                </button>
                            {/foreach}
                        </div>
                    {/if}
                </div>
            {else}
                <div class="dt-gallery-main"><div class="annuncio-card-ph">Nessuna foto</div></div>
            {/if}
        </section>

        <section class="pg-card dt-info">
            <div class="pg-meta-row dt-meta-row">
                <span class="pg-pill" data-tone="success"><span class="dt-dot"></span> Disponibile</span>
                {if !empty($annuncio.categoria_nome)}<span class="dt-cat">{$annuncio.categoria_nome}</span>{/if}
            </div>

            <h1 class="dt-title">{$annuncio.titolo|default:''}</h1>
            <div class="dt-price-row">
                <span class="dt-price">&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</span>
                <span class="dt-price-note">Pagamento e spedizione protetti da NerdVault</span>
            </div>

            <div class="dt-attrs">
                <div class="dt-attr"><span class="dt-attr-label">Condizione</span><span class="dt-attr-value">{$annuncio.stato_conservazione|default:'Non specificato'}</span></div>
                <div class="dt-attr"><span class="dt-attr-label">Categoria</span><span class="dt-attr-value">{$annuncio.categoria_nome|default:'Senza categoria'}</span></div>
                <div class="dt-attr"><span class="dt-attr-label">Stato vendita</span><span class="dt-attr-value">{$annuncio.stato|default:''|ucfirst}</span></div>
            </div>

            {if $isLogged}
                {if $isAdmin}
                    <div class="pg-alert" data-tone="info">Accesso admin: carrello, wishlist e acquisto sono disattivati.</div>
                {elseif $isBusiness && !$isOwner}
                    <div class="pg-alert" data-tone="info">Account business: puoi vendere prodotti, ma carrello, wishlist e acquisto sono disattivati.</div>
                    <div class="pg-actions"><a class="btn btn-secondary" href="/segnalazione/create/{$annuncioId}">Segnala</a></div>
                {elseif $isOwner}
                    <div class="pg-alert" data-tone="gold">Questo e un tuo annuncio: carrello e acquisto sono disattivati.</div>
                    {if ($annuncio.stato|default:'') == 'attivo'}
                        <div class="pg-actions">
                            <a class="btn" href="/annuncio/edit/{$annuncioId}">Modifica</a>
                            <a class="btn btn-danger" href="/annuncio/delete/{$annuncioId}">Elimina</a>
                        </div>
                    {/if}
                {else}
                    <div class="pg-actions dt-actions">
                        {if $isInCart}
                            <span class="btn" data-variant="dark" data-size="lg">Nel carrello</span>
                        {else}
                            <a class="btn dt-cart-btn" data-size="lg" href="/carrello/add/{$annuncioId}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="8" cy="21" r="1"></circle>
                                    <circle cx="19" cy="21" r="1"></circle>
                                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                </svg>
                                <span>Aggiungi al carrello</span>
                            </a>
                        {/if}
                        <a class="btn" data-variant="gold" data-size="lg" href="/pagamento/checkout/{$annuncioId}">Compra ora</a>
                        {if $canUseWishlist}
                            <a
                                class="dt-wishlist-btn {if $isInWishlist}is-active{/if}"
                                href="/wishlist/toggle/{$annuncioId}"
                                title="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}"
                                aria-label="{if $isInWishlist}Rimuovi dalla wishlist{else}Aggiungi alla wishlist{/if}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="{if $isInWishlist}currentColor{else}none{/if}" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 0 1 12 6a5 5 0 0 1 7.5 6.6z"></path>
                                </svg>
                            </a>
                        {/if}
                        <a class="btn btn-secondary" href="/segnalazione/create/{$annuncioId}">Segnala</a>
                    </div>
                {/if}
            {/if}

            <div class="dt-trust">
                <div class="dt-trust-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                    <span>Pagamento protetto fino alla consegna</span>
                </div>
                <div class="dt-trust-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                    <span>Reso entro 14 giorni</span>
                </div>
                <div class="dt-trust-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                    <span>Spedizione tracciata</span>
                </div>
            </div>

            <div class="dt-seller">
                <div class="dt-seller-head">
                    <span class="dt-seller-av" aria-hidden="true">{$sellerName|substr:0:1|strtoupper}</span>
                    <div class="dt-seller-body">
                        <a href="/utente/venditore/{$annuncioOwner}" class="dt-seller-name">
                            {$sellerName}
                            {if !empty($annuncio.venditore_business_id)}<span class="seller-pro-badge nv-pro-badge">PRO</span>{/if}
                        </a>
                        <div class="dt-seller-meta">
                            {if $feedbackCount > 0}
                                {include file="components/stars.tpl" value=$stelleVenditore}
                                <strong>{$mediaVenditore|number_format:1:",":"."}</strong>
                                <span class="muted">({$feedbackCount} recensioni)</span>
                            {else}
                                <span class="muted">Nessuna recensione</span>
                            {/if}
                        </div>
                    </div>
                    <a class="btn btn-secondary" data-size="sm" href="/utente/venditore/{$annuncioOwner}">Vedi profilo</a>
                </div>
                <div class="dt-seller-stats">
                    <div><strong>{$feedbackCount}</strong><span>recensioni</span></div>
                    <div><strong>{if $feedbackCount > 0}{$mediaVenditore|number_format:1:",":"."}{else}-{/if}</strong><span>valutazione</span></div>
                    <div><strong>OK</strong><span>feedback verificati</span></div>
                </div>
            </div>
        </section>
    </div>

    <div class="dt-detail-grid">
        <section class="pg-card">
            <h2 class="pg-card-title">Descrizione</h2>
            {if !empty($annuncio.descrizione)}
                <p class="dt-desc">{$annuncio.descrizione|default:''|nl2br_e nofilter}</p>
            {else}
                <p class="muted">Il venditore non ha aggiunto una descrizione.</p>
            {/if}
            <h3 class="dt-desc-sub">Cosa include</h3>
            <ul class="dt-desc-list">
                <li>Articolo come da descrizione dell'annuncio</li>
                <li>Imballaggio protetto preparato dal venditore</li>
                <li>Tracciamento ordine quando previsto dalla spedizione</li>
            </ul>
        </section>

        <aside class="pg-card dt-side-card">
            <h3 class="pg-card-title">Spedizione e pagamento</h3>
            <div class="dt-ship">
                <div class="dt-ship-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><div><strong>Pagamento protetto</strong><span>Fino alla conferma dell'acquirente</span></div></div>
                <div class="dt-ship-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><div><strong>Spedizione tracciata</strong><span>Gestita dal venditore</span></div></div>
                <div class="dt-ship-row"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg><div><strong>Feedback verificato</strong><span>Dopo la transazione</span></div></div>
            </div>
            <div class="pg-divider pg-divider-dashed"></div>
            <p class="dt-side-note">Pagamento via carta, PayPal o bonifico SEPA. La transazione resta protetta da NerdVault fino alla consegna.</p>
        </aside>
    </div>

    <section class="dt-feedback">
        <div class="dt-feedback-head">
            <div>
                <h2 class="pg-h1">Recensioni del venditore</h2>
                <p class="pg-sub">
                    {if $feedbackCount > 0}
                        <strong>{$mediaVenditore|number_format:1:",":"."}</strong> su 5 da {$feedbackCount} acquirenti
                    {else}
                        Questo venditore non ha ancora recensioni.
                    {/if}
                </p>
            </div>
            <a class="btn btn-secondary" data-size="sm" href="/feedback/venditore/{$annuncioOwner}">Vedi tutte</a>
        </div>

        {if !empty($feedbackVenditore)}
            <div class="dt-feedback-list">
                {foreach $feedbackVenditore as $item}
                    {if $item@iteration <= 3}
                        <article class="dt-feedback-item">
                            <div class="dt-feedback-row">
                                <span class="dt-feedback-av">{$item.autore|default:'U'|substr:0:1|strtoupper}</span>
                                <div class="dt-feedback-body">
                                    <div class="dt-feedback-meta">
                                        <strong>{$item.autore|default:'Acquirente'}</strong>
                                        {include file="components/stars.tpl" value=$item.valutazione|default:0}
                                        {if !empty($item.data_feedback)}<span class="muted">{$item.data_feedback}</span>{/if}
                                    </div>
                                    {if !empty($item.commento)}
                                        <p class="dt-feedback-text">{$item.commento}</p>
                                    {else}
                                        <p class="dt-feedback-text">Feedback positivo senza commento.</p>
                                    {/if}
                                </div>
                            </div>
                        </article>
                    {/if}
                {/foreach}
            </div>
        {else}
            <div class="pg-card dt-feedback-empty"><p class="muted">Nessuna recensione disponibile per questo venditore.</p></div>
        {/if}
    </section>

    <script>
    document.querySelectorAll('[data-gallery]').forEach(function (gallery) {
        const mainImage = gallery.querySelector('[data-gallery-main]');
        const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
        const prevButton = gallery.querySelector('[data-gallery-prev]');
        const nextButton = gallery.querySelector('[data-gallery-next]');
        const counter = gallery.querySelector('.dt-gallery-counter');
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
            if (counter) counter.textContent = (activeIndex + 1) + ' / ' + thumbs.length;
        }

        thumbs.forEach(function (thumb, index) {
            thumb.addEventListener('click', function () { showImage(index); });
        });
        if (prevButton) prevButton.addEventListener('click', function () { showImage(activeIndex - 1); });
        if (nextButton) nextButton.addEventListener('click', function () { showImage(activeIndex + 1); });
    });
    </script>
{else}
    <div class="pg-alert" data-tone="danger">Annuncio non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
