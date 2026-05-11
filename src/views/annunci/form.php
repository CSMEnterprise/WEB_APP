<?php
$pageTitle = $pageTitle ?? 'Nuovo annuncio';
$categorie = $categorie ?? [];
$annuncio = $annuncio ?? [];
$isEdit = !empty($annuncio);
$action = $action ?? ($isEdit ? 'index.php?route=annuncio-update' : 'index.php?route=annuncio-store');

require __DIR__ . '/../layout/header.php';
?>

<section class="card">
    <h1><?= e($pageTitle) ?></h1>

    <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? $annuncio->id_annuncio ?? '') ?>">
        <?php endif; ?>

        <label>
            Titolo
            <input type="text" name="titolo" required maxlength="255"
                   value="<?= e($annuncio['titolo'] ?? $annuncio->titolo ?? '') ?>">
        </label>

        <label>
            Descrizione
            <textarea name="descrizione" rows="6"><?= e($annuncio['descrizione'] ?? $annuncio->descrizione ?? '') ?></textarea>
        </label>

        <div class="grid-2">
            <label>
                Categoria
                <select name="id_categoria" required>
                    <option value="">Seleziona categoria</option>
                    <?php foreach ($categorie as $categoria): ?>
                        <?php $catId = $categoria['id_categoria'] ?? $categoria->id_categoria ?? ''; ?>
                        <option value="<?= e($catId) ?>"
                            <?= (string)($annuncio['id_categoria'] ?? $annuncio->id_categoria ?? '') === (string)$catId ? 'selected' : '' ?>>
                            <?= e($categoria['nome'] ?? $categoria->nome ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Prezzo
                <input type="number" name="prezzo" min="0" step="0.01" required
                       value="<?= e($annuncio['prezzo'] ?? $annuncio->prezzo ?? '') ?>">
            </label>
        </div>

        <div class="grid-2">
            <label>
                Stato conservazione
                <select name="stato_conservazione" required>
                    <?php foreach (['Nuovo','Ottimo','Buono','Discreto','Da restaurare'] as $stato): ?>
                        <option value="<?= e($stato) ?>"
                            <?= ($annuncio['stato_conservazione'] ?? $annuncio->stato_conservazione ?? '') === $stato ? 'selected' : '' ?>>
                            <?= e($stato) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Modalità consegna
                <select name="modalita_consegna" required>
                    <?php foreach (['Spedizione','Ritiro_a_mano','Entrambi'] as $modalita): ?>
                        <option value="<?= e($modalita) ?>"
                            <?= ($annuncio['modalita_consegna'] ?? $annuncio->modalita_consegna ?? '') === $modalita ? 'selected' : '' ?>>
                            <?= e(str_replace('_', ' ', $modalita)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <label>
            Immagini
            <input type="file" name="immagini[]" accept="image/*" multiple>
        </label>

        <button class="btn" type="submit">Salva annuncio</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
