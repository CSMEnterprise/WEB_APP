{* Form crea/modifica annuncio in stile NerdVault Pages.html con upload progressivo fino a 5 foto. *}
{include file="layouts/header.tpl"}

{assign var=isEdit value=$isEdit|default:false}
{if $isEdit}
    {assign var=formRoute value='annuncio/update'}
    {assign var=submitLabel value='Salva modifiche'}
    {assign var=pageTitle value='Modifica annuncio'}
{else}
    {assign var=formRoute value='annuncio/store'}
    {assign var=submitLabel value='Pubblica annuncio'}
    {assign var=pageTitle value='Crea un annuncio'}
{/if}
{assign var=titoloValue value=$post.titolo|default:$annuncio.titolo|default:''}
{assign var=descrizioneValue value=$post.descrizione|default:$annuncio.descrizione|default:''}
{assign var=categoriaValue value=$post.id_categoria|default:$annuncio.id_categoria|default:0}
{assign var=statoValue value=$post.stato_conservazione|default:$annuncio.stato_conservazione|default:'Nuovo'}
{assign var=prezzoValue value=$post.prezzo|default:$annuncio.prezzo|default:''}
{assign var=existingPhotoCount value=0}
{if $isEdit && !empty($annuncio.immagini)}
    {assign var=existingPhotoCount value=$annuncio.immagini|count_items}
{/if}

<nav class="pg-breadcrumb">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <a href="/utente/profilo">I miei annunci</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    <span class="current">{$pageTitle}</span>
</nav>

<div class="pg-head vd-create-head">
    <div>
        <h1 class="pg-h1">{$pageTitle}</h1>
        <p class="pg-sub">Pubblica un oggetto da collezione in pochi passaggi.</p>
    </div>
    <div class="vd-progress" aria-label="Campi completati">
        <div class="vd-progress-ring" data-vd-progress style="--p: 0%;">
            <span data-vd-progress-text>0/5</span>
        </div>
        <span class="pg-sub">campi completati</span>
    </div>
</div>

{if !empty($errore)}
    <div class="pg-alert" data-tone="danger">{$errore}</div>
{/if}

<div class="vd-layout">
    <form class="vd-form" method="post" action="/{$formRoute}" enctype="multipart/form-data" data-vd-form>
        {if $isEdit}
            <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:''}">
        {/if}

        <section class="pg-card vd-card">
            <h2 class="pg-card-title">Foto dell'oggetto</h2>
            <p class="pg-card-sub">Fino a 5 foto, JPG, PNG o WEBP. La prima sara la copertina.</p>

            {if $isEdit && !empty($annuncio.immagini)}
                <div class="vd-current-photos">
                    {foreach $annuncio.immagini as $immagine}
                        <div class="vd-photo vd-photo-existing {if $immagine@first}vd-photo-cover{/if}">
                            <img src="{$immagine.url|default:''}" alt="Foto annuncio">
                            {if $immagine@first}<span class="vd-photo-tag">Copertina</span>{/if}
                            <button
                                class="vd-photo-del"
                                type="submit"
                                form="delete-image-{$immagine.id_immagine|default:''}"
                                aria-label="Rimuovi foto"
                                title="Rimuovi foto">
                                &times;
                            </button>
                        </div>
                    {/foreach}
                </div>
            {/if}

            <div class="photo-upload-box vd-upload-box">
                <input
                    type="file"
                    id="immagini"
                    name="immagini[]"
                    data-preview="photo-preview"
                    data-existing-count="{$existingPhotoCount}"
                    data-max-files="5"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    class="vd-file-input">

                <label class="vd-photo-add photo-upload-btn" for="immagini" data-photo-start>
                    <span class="vd-photo-add-plus">+</span>
                    <span>Aggiungi foto</span>
                </label>
            </div>

            <div id="photo-preview" class="photo-preview photo-preview-progressive vd-photos" data-vd-photos></div>
            {if $isEdit}<p class="pg-help vd-photo-help">Puoi aggiungere nuove foto fino a 5 totali.</p>{/if}
        </section>

        <section class="pg-card vd-card">
            <h2 class="pg-card-title">Dettagli</h2>
            <p class="pg-card-sub">Piu sei preciso, prima vendi.</p>

            <div class="pg-field">
                <label class="pg-label" for="titolo">Titolo</label>
                <input class="pg-input" type="text" id="titolo" name="titolo" value="{$titoloValue}" maxlength="80" placeholder="es. Naruto Shippuden - cofanetto Vol. 1-12" required data-vd-title>
                <span class="pg-help"><span data-vd-title-count>0</span>/80 caratteri</span>
            </div>

            <div class="pg-field-row">
                <div class="pg-field">
                    <label class="pg-label" for="id_categoria">Categoria</label>
                    <select class="pg-select" id="id_categoria" name="id_categoria" required data-vd-category>
                        <option value="">Seleziona categoria</option>
                        {foreach $categorie as $categoria}
                            <option value="{$categoria.id_categoria}" {if $categoriaValue == $categoria.id_categoria}selected{/if}>{$categoria.nome}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="pg-field">
                    <label class="pg-label" for="stato_conservazione">Stato di conservazione</label>
                    <select class="pg-select" id="stato_conservazione" name="stato_conservazione" required data-vd-condition>
                        {foreach ['Nuovo','Usato come nuovo','Ottimo','Buono','Discreto','Scarso'] as $stato}
                            <option value="{$stato}" {if $statoValue == $stato}selected{/if}>{$stato}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            <div class="pg-field">
                <label class="pg-label" for="descrizione">Descrizione</label>
                <textarea class="pg-textarea" id="descrizione" name="descrizione" placeholder="Descrivi condizioni, completezza, difetti, provenienza..." required data-vd-description>{$descrizioneValue}</textarea>
            </div>

            <div class="pg-field vd-price-field">
                <label class="pg-label" for="prezzo">Prezzo</label>
                <div class="pg-input-prefix">
                    <span class="px">&euro;</span>
                    <input class="pg-input" type="number" id="prezzo" name="prezzo" min="0.01" step="0.01" value="{$prezzoValue}" placeholder="0,00" required data-vd-price>
                </div>
                <span class="pg-help">Commissione NerdVault 5% alla vendita.</span>
            </div>
        </section>

        <div class="pg-alert" data-tone="info">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v4"></path><path d="M12 18v4"></path><path d="m4.93 4.93 2.83 2.83"></path><path d="m16.24 16.24 2.83 2.83"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="m4.93 19.07 2.83-2.83"></path><path d="m16.24 7.76 2.83-2.83"></path></svg>
            <span>Gli annunci con almeno 3 foto e descrizione completa vendono in media piu velocemente.</span>
        </div>

        <div class="pg-form-actions vd-actions-create">
            <button class="btn" data-size="lg" type="submit">{$submitLabel}</button>
            <a class="btn btn-secondary" data-size="lg" href="/utente/profilo">Annulla</a>
        </div>
    </form>

    <aside class="vd-side">
        <div class="vd-preview-wrap">
            <span class="vd-preview-label">Anteprima live</span>
            <article class="va-card vd-preview-card">
                <div class="va-card-media">
                    <div class="vd-preview-img" data-vd-preview-img>NV</div>
                    <button type="button" class="wishlist-heart nv-heart vd-preview-heart" aria-label="wishlist">&hearts;</button>
                </div>
                <div class="va-card-body">
                    <div class="va-card-meta">
                        <span class="va-card-cat" data-vd-preview-category>Categoria</span>
                        <span>&middot; <span data-vd-preview-condition>{$statoValue}</span></span>
                    </div>
                    <h3 class="va-card-title is-placeholder" data-vd-preview-title>Titolo del tuo annuncio</h3>
                    <div class="va-card-seller">
                        <span class="va-card-seller-av">M</span>
                        <span class="va-card-seller-name">Venditore</span>
                        <span class="va-card-rating">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
                            5.0
                        </span>
                    </div>
                    <div class="va-card-foot">
                        <span class="va-card-price is-placeholder" data-vd-preview-price>&euro; --</span>
                        <button class="btn" data-size="sm" type="button" disabled>Al carrello</button>
                    </div>
                </div>
            </article>

            <div class="vd-tips">
                <span class="ls-filter-label">Checklist</span>
                <div class="vd-tip" data-vd-check="photo"><span class="vd-tip-check">✓</span>Almeno una foto</div>
                <div class="vd-tip" data-vd-check="title"><span class="vd-tip-check">✓</span>Titolo descrittivo</div>
                <div class="vd-tip" data-vd-check="category"><span class="vd-tip-check">✓</span>Categoria selezionata</div>
                <div class="vd-tip" data-vd-check="description"><span class="vd-tip-check">✓</span>Descrizione completa</div>
                <div class="vd-tip" data-vd-check="price"><span class="vd-tip-check">✓</span>Prezzo impostato</div>
            </div>
        </div>
    </aside>
</div>

{if $isEdit && !empty($annuncio.immagini)}
    {foreach $annuncio.immagini as $immagine}
        <form
            id="delete-image-{$immagine.id_immagine|default:''}"
            method="post"
            action="/annuncio/image-delete"
            class="u-style-013">
            <input type="hidden" name="id_immagine" value="{$immagine.id_immagine|default:''}">
        </form>
    {/foreach}
{/if}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('immagini');
    const preview = document.getElementById('photo-preview');
    const form = document.querySelector('[data-vd-form]');
    const startButton = document.querySelector('[data-photo-start]');
    const titleInput = document.querySelector('[data-vd-title]');
    const categoryInput = document.querySelector('[data-vd-category]');
    const conditionInput = document.querySelector('[data-vd-condition]');
    const descInput = document.querySelector('[data-vd-description]');
    const priceInput = document.querySelector('[data-vd-price]');
    const selectedFiles = [];

    if (!input || !preview || !form || !startButton) return;

    const maxFiles = parseInt(input.dataset.maxFiles || '5', 10);
    const existingCount = parseInt(input.dataset.existingCount || '0', 10);
    const maxSelectable = Math.max(maxFiles - existingCount, 0);

    function formatPrice(value) {
        const numeric = parseFloat(value);
        if (!numeric || numeric <= 0) return '&euro; --';
        return '&euro; ' + numeric.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateLivePreview() {
        const title = titleInput.value.trim();
        const desc = descInput.value.trim();
        const category = categoryInput.options[categoryInput.selectedIndex] ? categoryInput.options[categoryInput.selectedIndex].text : '';
        const hasCategory = categoryInput.value !== '';
        const hasPhoto = existingCount > 0 || selectedFiles.length > 0;
        const checks = {
            photo: hasPhoto,
            title: title.length > 5,
            category: hasCategory,
            description: desc.length > 10,
            price: parseFloat(priceInput.value) > 0
        };
        const complete = Object.keys(checks).filter(function (key) { return checks[key]; }).length;

        document.querySelector('[data-vd-preview-title]').textContent = title || 'Titolo del tuo annuncio';
        document.querySelector('[data-vd-preview-title]').classList.toggle('is-placeholder', !title);
        document.querySelector('[data-vd-preview-category]').textContent = hasCategory ? category : 'Categoria';
        document.querySelector('[data-vd-preview-condition]').textContent = conditionInput.value;
        document.querySelector('[data-vd-preview-price]').innerHTML = formatPrice(priceInput.value);
        document.querySelector('[data-vd-preview-price]').classList.toggle('is-placeholder', !checks.price);
        document.querySelector('[data-vd-title-count]').textContent = titleInput.value.length;
        document.querySelector('[data-vd-progress]').style.setProperty('--p', (complete / 5 * 100) + '%');
        document.querySelector('[data-vd-progress-text]').textContent = complete + '/5';

        Object.keys(checks).forEach(function (key) {
            const item = document.querySelector('[data-vd-check="' + key + '"]');
            if (item) item.classList.toggle('is-ok', checks[key]);
        });
    }

    function openFilePicker() {
        if (selectedFiles.length >= maxSelectable) return;
        input.value = '';
        input.click();
    }

    function syncInputFiles() {
        if (typeof DataTransfer === 'undefined') return;
        try {
            const transfer = new DataTransfer();
            selectedFiles.forEach(function (file) {
                transfer.items.add(file);
            });
            input.files = transfer.files;
        } catch (error) {
            // Alcuni browser limitano l'assegnazione programmatica a input.files.
        }
    }

    function renderPreview() {
        preview.innerHTML = '';

        selectedFiles.forEach(function (file, index) {
            const item = document.createElement('div');
            item.className = 'photo-preview-item vd-photo' + (existingCount === 0 && index === 0 ? ' vd-photo-cover' : '');
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            img.onload = function () {
                URL.revokeObjectURL(img.src);
            };

            if (existingCount === 0 && index === 0) {
                const tag = document.createElement('span');
                tag.className = 'vd-photo-tag';
                tag.textContent = 'Copertina';
                item.appendChild(tag);
            }

            const removeButton = document.createElement('button');
            removeButton.className = 'photo-preview-delete vd-photo-del';
            removeButton.type = 'button';
            removeButton.innerHTML = '&times;';
            removeButton.title = 'Rimuovi foto';
            removeButton.setAttribute('aria-label', 'Rimuovi foto');
            removeButton.addEventListener('click', function () {
                selectedFiles.splice(index, 1);
                syncInputFiles();
                renderPreview();
            });

            item.appendChild(img);
            item.appendChild(removeButton);
            preview.appendChild(item);

            if (index === 0) {
                const live = document.querySelector('[data-vd-preview-img]');
                live.innerHTML = '';
                const liveImg = document.createElement('img');
                liveImg.src = URL.createObjectURL(file);
                liveImg.alt = '';
                liveImg.onload = function () {
                    URL.revokeObjectURL(liveImg.src);
                };
                live.appendChild(liveImg);
            }
        });

        if (selectedFiles.length === 0 && existingCount === 0) {
            document.querySelector('[data-vd-preview-img]').textContent = 'NV';
        }

        startButton.hidden = selectedFiles.length >= maxSelectable;
        updateLivePreview();
    }

    startButton.addEventListener('click', function () {
        if (selectedFiles.length < maxSelectable) {
            input.value = '';
        }
    });

    input.addEventListener('change', function () {
        const incomingFiles = Array.from(input.files || []);
        if (incomingFiles.length === 0) return;

        const remainingSlots = maxSelectable - selectedFiles.length;
        if (remainingSlots <= 0) {
            syncInputFiles();
            renderPreview();
            return;
        }

        incomingFiles.slice(0, remainingSlots).forEach(function (file) {
            if (file.type.startsWith('image/')) {
                selectedFiles.push(file);
            }
        });

        syncInputFiles();
        renderPreview();
    });

    [titleInput, categoryInput, conditionInput, descInput, priceInput].forEach(function (field) {
        field.addEventListener('input', updateLivePreview);
        field.addEventListener('change', updateLivePreview);
    });
    form.addEventListener('submit', syncInputFiles);
    renderPreview();
});
</script>

{include file="layouts/footer.tpl"}
