<?php
$pageTitle = 'Annunci';
require __DIR__ . '/../layout/header.php';
?>

<div class="nav" style="align-items:flex-start;">
    <h1>Annunci disponibili</h1>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a class="btn" href="index.php?route=annuncio-create">Crea annuncio</a>
    <?php endif; ?>
</div>

<?php if (!empty($annunci)): ?>
    <section class="grid">
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

                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a class="btn btn-secondary" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Carrello</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <div class="card">
        <p>Nessun annuncio disponibile.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
