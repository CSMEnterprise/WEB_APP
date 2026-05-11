<?php
$pageTitle = 'Annunci';
$annunci = $annunci ?? [];
$categorie = $categorie ?? [];
require __DIR__ . '/../layout/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Annunci</h1>
        <p class="muted">Sfoglia gli articoli disponibili su NerdVault.</p>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn" href="index.php?route=annuncio-create">Nuovo annuncio</a>
    <?php endif; ?>
</div>

<form method="get" action="index.php" class="filters card">
    <input type="hidden" name="route" value="annunci">

    <label>
        Cerca
        <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Titolo, descrizione...">
    </label>

    <label>
        Categoria
        <select name="categoria">
            <option value="">Tutte</option>
            <?php foreach ($categorie as $categoria): ?>
                <option value="<?= e($categoria['id_categoria'] ?? $categoria->id_categoria ?? '') ?>"
                    <?= (string)($_GET['categoria'] ?? '') === (string)($categoria['id_categoria'] ?? $categoria->id_categoria ?? '') ? 'selected' : '' ?>>
                    <?= e($categoria['nome'] ?? $categoria->nome ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button class="btn btn-secondary" type="submit">Filtra</button>
</form>

<?php if (empty($annunci)): ?>
    <div class="empty-state">
        <h2>Nessun annuncio trovato</h2>
        <p>Quando saranno presenti annunci attivi, li vedrai in questa pagina.</p>
    </div>
<?php else: ?>
    <section class="cards-grid">
        <?php foreach ($annunci as $annuncio): ?>
            <?php
                $id = $annuncio['id_annuncio'] ?? $annuncio->id_annuncio ?? '';
                $titolo = $annuncio['titolo'] ?? $annuncio->titolo ?? 'Annuncio';
                $prezzo = $annuncio['prezzo'] ?? $annuncio->prezzo ?? 0;
                $stato = $annuncio['stato_conservazione'] ?? $annuncio->stato_conservazione ?? '';
                $img = $annuncio['immagine'] ?? $annuncio['url'] ?? null;
            ?>
            <article class="product-card">
                <a href="index.php?route=annuncio&id=<?= e($id) ?>" class="product-image">
                    <?php if ($img): ?>
                        <img src="<?= e($img) ?>" alt="<?= e($titolo) ?>">
                    <?php else: ?>
                        <span>NerdVault</span>
                    <?php endif; ?>
                </a>

                <div class="product-body">
                    <h2><?= e($titolo) ?></h2>
                    <p class="muted"><?= e($stato) ?></p>
                    <p class="price">€ <?= number_format((float)$prezzo, 2, ',', '.') ?></p>

                    <div class="card-actions">
                        <a class="btn btn-small" href="index.php?route=annuncio&id=<?= e($id) ?>">Dettagli</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="btn btn-small btn-secondary" href="index.php?route=carrello-add&id=<?= e($id) ?>">Carrello</a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
