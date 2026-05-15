<?php
$pageTitle = 'Crea annuncio';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Crea annuncio</h1>

    <form method="post" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="route" value="annuncio-store">
        <label for="titolo">Titolo</label>
        <input type="text" id="titolo" name="titolo" required>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" required></textarea>

        <label for="id_categoria">Categoria</label>
        <select id="id_categoria" name="id_categoria" required>
            <option value="">Seleziona categoria</option>
            <?php foreach (($categorie ?? []) as $categoria): ?>
                <option value="<?= e($categoria['id_categoria'] ?? '') ?>">
                    <?= e($categoria['nome'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="stato_conservazione">Stato conservazione</label>
        <select id="stato_conservazione" name="stato_conservazione" required>
            <option value="Nuovo">Nuovo</option>
            <option value="Usato come nuovo">Usato come nuovo</option>
            <option value="Ottimo">Ottimo</option>
            <option value="Buono">Buono</option>
            <option value="Discreto">Discreto</option>
            <option value="Scarso">Scarso</option>
        </select>

        <label for="prezzo">Prezzo</label>
        <input type="number" id="prezzo" name="prezzo" min="0.01" step="0.01" required>

        <label>Foto annuncio</label>
        <div class="photo-upload-box">
            <input
                type="file"
                id="immagini"
                name="immagini[]"
                accept="image/jpeg,image/png,image/webp"
                multiple
                hidden
            >

            <label class="btn btn-secondary photo-upload-btn" for="immagini">
                Scegli foto
            </label>

            <span class="muted">Massimo 5 foto, formato JPG, PNG o WEBP.</span>
        </div>

        <div id="photo-preview" class="photo-preview"></div>

        <button class="btn" type="submit">Pubblica</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('immagini');
    const preview = document.getElementById('photo-preview');
    const maxFiles = 5;

    input.addEventListener('change', function () {
        preview.innerHTML = '';

        const files = Array.from(input.files).slice(0, maxFiles);

        files.forEach(function (file) {
            if (!file.type.startsWith('image/')) {
                return;
            }

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

<?php require __DIR__ . '/../layout/footer.php'; ?>
