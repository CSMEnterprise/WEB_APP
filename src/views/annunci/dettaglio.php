<?php
$pageTitle = $annuncio['titolo'] ?? 'Dettaglio annuncio';
require __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($annuncio)): ?>
    <?php $isOwner = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && (int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0); ?>

    <article class="card">
        <h1><?= e($annuncio['titolo'] ?? '') ?></h1>

        <?php if (!empty($annuncio['immagini'])): ?>
            <div class="annuncio-gallery">
                <?php foreach ($annuncio['immagini'] as $immagine): ?>
                    <img src="<?= e($immagine['url'] ?? '') ?>" alt="Foto annuncio">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="muted"><?= e($annuncio['categoria_nome'] ?? '') ?></p>
        <p><?= nl2br(e($annuncio['descrizione'] ?? '')) ?></p>

        <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
        <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?? '') ?></p>
        <p><strong>Stato vendita:</strong> <?= e(ucfirst((string)($annuncio['stato'] ?? ''))) ?></p>
        <p><strong>Venditore:</strong> <?= e($annuncio['venditore_username'] ?? '') ?></p>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <?php if ($isOwner): ?>
                <div class="alert alert-success">Questo è un tuo annuncio: carrello e acquisto sono disattivati.</div>
                <a class="btn btn-danger" href="index.php?route=annuncio-delete&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Elimina</a>
            <?php else: ?>
                <a class="btn" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                <a class="btn btn-secondary" href="index.php?route=wishlist-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi alla wishlist</a>
                <a class="btn btn-secondary" href="index.php?route=checkout&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Acquista</a>
                <a class="btn btn-secondary" href="index.php?route=segnalazione-create&id_annuncio=<?= e($annuncio['id_annuncio'] ?? '') ?>">Segnala</a>
            <?php endif; ?>
        <?php endif; ?>
    </article>
<?php else: ?>
    <div class="alert alert-error">Annuncio non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
