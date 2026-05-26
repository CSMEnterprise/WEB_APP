{include file="layouts/header.tpl"}

{assign var=isEdit value=$isEdit|default:false}
{if $isEdit}
    {assign var=formRoute value='annuncio-update'}
    {assign var=submitLabel value='Salva modifiche'}
{else}
    {assign var=formRoute value='annuncio-store'}
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

    <form method="post" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="route" value="{$formRoute}">
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
                accept="image/jpeg,image/png,image/webp"
                multiple
                hidden>

            <label class="btn btn-secondary photo-upload-btn" for="immagini">Scegli foto</label>
            <span class="muted">{if $isEdit}Puoi aggiungere nuove foto. Massimo 5 foto totali salvate per invio.{else}Massimo 5 foto, formato JPG, PNG o WEBP.{/if}</span>
        </div>

        <div id="photo-preview" class="photo-preview"></div>

        <button class="btn" type="submit">{$submitLabel}</button>
    </form>

    {if $isEdit && !empty($annuncio.immagini)}
        {foreach $annuncio.immagini as $immagine}
            <form
                id="delete-image-{$immagine.id_immagine|default:''}"
                method="post"
                action="index.php"
                class="u-style-013">
                <input type="hidden" name="route" value="annuncio-image-delete">
                <input type="hidden" name="id_immagine" value="{$immagine.id_immagine|default:''}">
            </form>
        {/foreach}
    {/if}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('immagini');
    const preview = document.getElementById('photo-preview');
    const maxFiles = 5;

    if (!input || !preview) return;

    input.addEventListener('change', function () {
        preview.innerHTML = '';
        Array.from(input.files).slice(0, maxFiles).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                const item = document.createElement('div');
                item.className = 'photo-preview-item';
                const img = document.createElement('img');
                img.src = event.target.result;
                img.alt = file.name;
                item.appendChild(img);
                preview.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>

{include file="layouts/footer.tpl"}
