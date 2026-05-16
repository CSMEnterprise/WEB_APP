<?php
$pageTitle = 'Wishlist';
require __DIR__ . '/../layout/header.php';
?>

<div class="nav" style="align-items:flex-start;">
    <h1>Wishlist</h1>

    <?php if (!empty($wishlist)): ?>
        <a class="btn btn-danger" href="index.php?route=wishlist-clear">Svuota wishlist</a>
    <?php endif; ?>
</div>

<?php if (!empty($wishlist)): ?>
    <section class="grid">
        <?php foreach ($wishlist as $annuncio): ?>
            <article
                class="card clickable-card"
                data-href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                role="link"
                tabindex="0">
                <?php if (!empty($annuncio['immagine_principale'])): ?>
                    <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                <?php endif; ?>

                <h2><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h2>
                <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                <p><?= e($annuncio['descrizione'] ?? '') ?></p>
                <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?: 'Non specificato') ?></p>
                <p>
                    <strong>Venditore:</strong>
                    <a href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                        <span class="seller-name-line">
                            <?= e(!empty($annuncio['venditore_business_id']) ? ($annuncio['venditore_nome_azienda'] ?? '') : ($annuncio['venditore_username'] ?? '')) ?>
                            <?php if (!empty($annuncio['venditore_business_id'])): ?>
                                <span class="seller-pro-badge">PRO</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </p>

                <div class="cart-item-actions">
                    <a class="btn btn-secondary" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>
                    <a class="btn" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <div class="card">
        <p>La wishlist è vuota.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
