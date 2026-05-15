<?php
$pageTitle = 'Annunci';
require __DIR__ . '/../layout/header.php';

$q = trim($_GET['q'] ?? '');
$idCategoria = (int)($_GET['id_categoria'] ?? 0);
?>

<div class="nav" style="align-items:flex-start;">
    <h1><?= ($q !== '' || $idCategoria > 0) ? 'Risultati ricerca' : 'Annunci disponibili' ?></h1>

    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin'])): ?>
        <a class="btn" href="index.php?route=annuncio-create">Crea annuncio</a>
    <?php endif; ?>
</div>

<?php if ($q !== '' || $idCategoria > 0): ?>
    <p class="muted">
        <?php if ($q !== ''): ?>
            Testo: "<?= e($q) ?>"
        <?php endif; ?>
        <?php if ($idCategoria > 0): ?>
            <?php
            $categoriaSelezionata = '';
            foreach (($categorie ?? []) as $categoria) {
                if ((int)($categoria['id_categoria'] ?? 0) === $idCategoria) {
                    $categoriaSelezionata = (string)($categoria['nome'] ?? '');
                    break;
                }
            }
            ?>
            <?= $q !== '' ? ' - ' : '' ?>Categoria: <?= e($categoriaSelezionata) ?>
        <?php endif; ?>
    </p>
<?php endif; ?>

<?php if ($q !== '' && !empty($utenti)): ?>
    <section style="margin-bottom: 32px;">
        <h2>Utenti trovati</h2>
        <div class="grid">
            <?php foreach ($utenti as $u): ?>
                <div class="card" style="display:flex; align-items:center; gap:14px;">
                    <div style="font-size:36px;">👤</div>
                    <div>
                        <strong><?= e($u['username'] ?? '') ?></strong>
                        <?php if (!empty($u['nome'])): ?>
                            <p class="muted" style="margin:2px 0;"><?= e($u['nome']) ?></p>
                        <?php endif; ?>
                        <a class="btn btn-secondary"
                           style="font-size:12px;padding:5px 10px;margin-top:6px;display:inline-block;"
                           href="index.php?route=feedback-venditore&id=<?= e($u['id_utente']) ?>">
                            Vedi feedback
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section>
    <?php if ($q !== ''): ?>
        <h2>Annunci trovati</h2>
    <?php endif; ?>

    <?php if (!empty($annunci)): ?>
        <div class="grid">
            <?php foreach ($annunci as $annuncio): ?>
                <article class="card">
                    <?php if (!empty($annuncio['immagine_principale'])): ?>
                        <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                    <?php endif; ?>

                    <h2><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h2>
                    <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                    <p><?= e($annuncio['descrizione'] ?? '') ?></p>
                    <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <p><strong>Stato:</strong> <?= e($annuncio['stato_conservazione'] ?? '') ?></p>
                    <p><strong>Venditore:</strong> <?= e($annuncio['venditore_username'] ?? '') ?></p>

                    <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin'])): ?>
                        <?php if ((int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)): ?>
                            <p class="muted">È un tuo annuncio.</p>
                        <?php else: ?>
                            <a class="btn btn-secondary" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                            <a class="btn btn-secondary" href="index.php?route=wishlist-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi alla wishlist</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <?php if ($q !== ''): ?>
                <p>Nessun annuncio trovato per "<?= e($q) ?>".</p>
            <?php else: ?>
                <p>Nessun annuncio disponibile.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
