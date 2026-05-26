<?php
$isEdit = !empty($isEdit);
$annuncio = $annuncio ?? [];
$pageTitle = $isEdit ? 'Modifica annuncio' : 'Crea annuncio';
$formRoute = $isEdit ? 'annuncio-update' : 'annuncio-store';
$submitLabel = $isEdit ? 'Salva modifiche' : 'Pubblica';
$titoloValue = $_POST['titolo'] ?? ($annuncio['titolo'] ?? '');
$descrizioneValue = $_POST['descrizione'] ?? ($annuncio['descrizione'] ?? '');
$categoriaValue = (int)($_POST['id_categoria'] ?? ($annuncio['id_categoria'] ?? 0));
$statoValue = $_POST['stato_conservazione'] ?? ($annuncio['stato_conservazione'] ?? 'Nuovo');
$prezzoValue = $_POST['prezzo'] ?? ($annuncio['prezzo'] ?? '');
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1><?= e($pageTitle) ?></h1>

    <form method="post" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="route" value="<?= e($formRoute) ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? '') ?>">
        <?php endif; ?>

        <label for="titolo">Titolo</label>
        <input type="text" id="titolo" name="titolo" value="<?= e($titoloValue) ?>" required>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" required><?= e($descrizioneValue) ?></textarea>

        <label for="id_categoria">Categoria</label>
        <select id="id_categoria" name="id_categoria" required>
            <option value="">Seleziona categoria</option>
            <?php foreach (($categorie ?? []) as $categoria): ?>
                <option
                    value="<?= e($categoria['id_categoria'] ?? '') ?>"
                    <?= $categoriaValue === (int)($categoria['id_categoria'] ?? 0) ? 'selected' : '' ?>>
                    <?= e($categoria['nome'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="stato_conservazione">Stato conservazione</label>
        <select id="stato_conservazione" name="stato_conservazione" required>
            <?php foreach (['Nuovo', 'Usato come nuovo', 'Ottimo', 'Buono', 'Discreto', 'Scarso'] as $stato): ?>
                <option value="<?= e($stato) ?>" <?= $statoValue === $stato ? 'selected' : '' ?>>
                    <?= e($stato) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="prezzo">Prezzo</label>
        <input type="number" id="prezzo" name="prezzo" min="0.01" step="0.01" value="<?= e($prezzoValue) ?>" required>

        <?php if ($isEdit && !empty($annuncio['immagini'])): ?>
            <label>Foto attuali</label>
            <div class="current-photo-list">
                <?php foreach ($annuncio['immagini'] as $immagine): ?>
                    <div class="current-photo-item">
                        <img src="<?= e($immagine['url'] ?? '') ?>" alt="Foto annuncio">
                        <button
                            class="current-photo-delete"
                            type="submit"
                            form="delete-image-<?= e($immagine['id_immagine'] ?? '') ?>"
                            aria-label="Rimuovi foto"
                            title="Rimuovi foto">
                            &times;
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label>Foto annuncio</label>
        <div class="photo-upload-box">
            <input
                type="file"
                id="immagini"
                name="immagini[]"
                data-preview="photo-preview"
                accept="image/jpeg,image/png,image/webp"
                multiple
                hidden
            >

            <label class="btn btn-secondary photo-upload-btn" for="immagini">
                Scegli foto
            </label>

            <span class="muted">
                <?= $isEdit ? 'Puoi aggiungere nuove foto. Massimo 5 foto totali salvate per invio.' : 'Massimo 5 foto, formato JPG, PNG o WEBP.' ?>
            </span>
        </div>

        <div id="photo-preview" class="photo-preview"></div>

        <button class="btn" type="submit"><?= e($submitLabel) ?></button>
    </form>

    <?php if ($isEdit && !empty($annuncio['immagini'])): ?>
        <?php foreach ($annuncio['immagini'] as $immagine): ?>
            <form
                id="delete-image-<?= e($immagine['id_immagine'] ?? '') ?>"
                method="post"
                action="index.php"
                class="u-style-013">
                <input type="hidden" name="route" value="annuncio-image-delete">
                <input type="hidden" name="id_immagine" value="<?= e($immagine['id_immagine'] ?? '') ?>">
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
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
