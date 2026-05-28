{* Form crea/modifica annuncio: il flag $isEdit determina route, label submit e visibilità delle foto già salvate. Gestisce l'upload progressivo fino a 5 foto tramite JS con DataTransfer. *}
{include file="layouts/header.tpl"}

{* Impostazione route e label in base alla modalità create/edit *}
{assign var=isEdit value=$isEdit|default:false}
{if $isEdit}
    {assign var=formRoute value='annuncio/update'}
    {assign var=submitLabel value='Salva modifiche'}
{else}
    {assign var=formRoute value='annuncio/store'}
    {assign var=submitLabel value='Pubblica'}
{/if}
{assign var=titoloValue value=$post.titolo|default:$annuncio.titolo|default:''}
{assign var=descrizioneValue value=$post.descrizione|default:$annuncio.descrizione|default:''}
{assign var=categoriaValue value=$post.id_categoria|default:$annuncio.id_categoria|default:0}
{assign var=statoValue value=$post.stato_conservazione|default:$annuncio.stato_conservazione|default:'Nuovo'}
{assign var=prezzoValue value=$post.prezzo|default:$annuncio.prezzo|default:''}

<div class="card">
    <h1>{if $isEdit}Modifica annuncio{else}Crea annuncio{/if}</h1>

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="/{$formRoute}" enctype="multipart/form-data">
                {if $isEdit}
            <input type="hidden" name="id_annuncio" value="{$annuncio.id_annuncio|default:''}">
        {/if}

        <label for="titolo">Titolo</label>
        <input type="text" id="titolo" name="titolo" value="{$titoloValue}" required>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" required>{$descrizioneValue}</textarea>

        <label for="id_categoria">Categoria</label>
        <select id="id_categoria" name="id_categoria" required>
            <option value="">Seleziona categoria</option>
            {foreach $categorie as $categoria}
                <option value="{$categoria.id_categoria}" {if $categoriaValue == $categoria.id_categoria}selected{/if}>{$categoria.nome}</option>
            {/foreach}
        </select>

        <label for="stato_conservazione">Stato conservazione</label>
        <select id="stato_conservazione" name="stato_conservazione" required>
            {foreach ['Nuovo','Usato come nuovo','Ottimo','Buono','Discreto','Scarso'] as $stato}
                <option value="{$stato}" {if $statoValue == $stato}selected{/if}>{$stato}</option>
            {/foreach}
        </select>

        <label for="prezzo">Prezzo</label>
        <input type="number" id="prezzo" name="prezzo" min="0.01" step="0.01" value="{$prezzoValue}" required>

        {if $isEdit && !empty($annuncio.immagini)}
            <label>Foto attuali</label>
            <div class="current-photo-list">
                {foreach $annuncio.immagini as $immagine}
                    <div class="current-photo-item">
                        <img src="{$immagine.url|default:''}" alt="Foto annuncio">
                        <button
                            class="current-photo-delete"
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

        <label>Foto annuncio</label>
        <div class="photo-upload-box">
            <input
                type="file"
                id="immagini"
                name="immagini[]"
                data-preview="photo-preview"
                data-existing-count="{if $isEdit && !empty($annuncio.immagini)}{$annuncio.immagini|count_items}{else}0{/if}"
                data-max-files="5"
                accept="image/jpeg,image/png,image/webp"
                multiple
                hidden>

            <button class="btn btn-secondary photo-upload-btn" type="button" data-photo-start>Scegli foto</button>
            <span class="muted">{if $isEdit}Puoi aggiungere nuove foto. Massimo 5 foto totali salvate per invio.{else}Massimo 5 foto, formato JPG, PNG o WEBP.{/if}</span>
        </div>

        <div id="photo-preview" class="photo-preview photo-preview-progressive"></div>

        <button class="btn" type="submit">{$submitLabel}</button>
    </form>

    {* Form nascosti per l'eliminazione delle singole foto: vengono submittati dal pulsante × della foto corrispondente *}
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('immagini');
    const preview = document.getElementById('photo-preview');
    const form = input ? input.closest('form') : null;
    const startButton = document.querySelector('[data-photo-start]');

    if (!input || !preview || !form || !startButton) return;

    const maxFiles = parseInt(input.dataset.maxFiles || '5', 10);
    const existingCount = parseInt(input.dataset.existingCount || '0', 10);
    const maxSelectable = Math.max(maxFiles - existingCount, 0);
    const selectedFiles = [];
    const addButton = document.createElement('button');

    addButton.className = 'photo-add-more';
    addButton.type = 'button';
    addButton.textContent = '+';
    addButton.title = 'Aggiungi un\'altra foto';
    addButton.setAttribute('aria-label', 'Aggiungi un\'altra foto');

    function openFilePicker() {
        if (selectedFiles.length >= maxSelectable) return;
        input.value = '';
        input.click();
    }

    function syncInputFiles() {
        const transfer = new DataTransfer();
        selectedFiles.forEach(function (file) {
            transfer.items.add(file);
        });
        input.files = transfer.files;
    }

    function renderPreview() {
        preview.innerHTML = '';

        selectedFiles.forEach(function (file, index) {
            const item = document.createElement('div');
            item.className = 'photo-preview-item';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            img.onload = function () {
                URL.revokeObjectURL(img.src);
            };
            const removeButton = document.createElement('button');
            removeButton.className = 'photo-preview-delete';
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
        });

        startButton.hidden = selectedFiles.length > 0 || maxSelectable <= 0;
        addButton.hidden = selectedFiles.length === 0 || selectedFiles.length >= maxSelectable;

        if (!addButton.hidden) {
            preview.appendChild(addButton);
        }
    }

    startButton.addEventListener('click', openFilePicker);
    addButton.addEventListener('click', openFilePicker);

    input.addEventListener('change', function () {
        const remainingSlots = maxSelectable - selectedFiles.length;
        if (remainingSlots <= 0) {
            syncInputFiles();
            renderPreview();
            return;
        }

        Array.from(input.files).slice(0, remainingSlots).forEach(function (file) {
            if (file.type.startsWith('image/')) {
                selectedFiles.push(file);
            }
        });

        syncInputFiles();
        renderPreview();
    });

    form.addEventListener('submit', syncInputFiles);
    renderPreview();
});
</script>

{include file="layouts/footer.tpl"}
