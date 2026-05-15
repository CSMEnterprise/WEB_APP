<?php
$pageTitle = 'Crea annuncio';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Crea annuncio</h1>

    <form method="post" action="index.php?route=annuncio-store">
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
            <option value="Usato">Usato</option>
        </select>

        <label for="prezzo">Prezzo</label>
        <input type="number" id="prezzo" name="prezzo" min="0.01" step="0.01" required>

        <label for="modalita_consegna">Modalità consegna</label>
        <select id="modalita_consegna" name="modalita_consegna" required>
            <option value="Consegna">Consegna</option>
        </select>

        <button class="btn" type="submit">Pubblica</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
