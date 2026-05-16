<?php
$pageTitle = 'Home';
require __DIR__ . '/layout/header.php';
?>

<section class="card">
    <h1>Compra e vendi articoli nerd in modo semplice.</h1>
    <p>
        NerdVault è un marketplace per videogiochi, fumetti, action figure, carte collezionabili,
        gadget e prodotti da collezione.
    </p>

    <p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="btn btn-secondary" href="index.php?route=register">Crea account</a>
        <?php endif; ?>
    </p>
</section>

<section style="margin-top: 28px;">
    <div class="nav" style="align-items:flex-start;">
        <h2><?= e($homeTitoloAnnunci ?? 'Annunci in evidenza') ?></h2>
        <a class="btn btn-secondary" href="index.php?route=annunci">Vedi tutti</a>
    </div>

    <?php if (!empty($homeAnnunci)): ?>
        <div class="grid">
            <?php foreach ($homeAnnunci as $annuncio): ?>
                <article
                    class="card clickable-card annuncio-card"
                    data-href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                    role="link"
                    tabindex="0">
                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']) && (int)($annuncio['id_utente'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)): ?>
                        <?php $isInWishlist = in_array((int)($annuncio['id_annuncio'] ?? 0), $wishlistIds ?? [], true); ?>
                        <a
                            class="wishlist-heart <?= $isInWishlist ? 'wishlist-heart-active' : '' ?>"
                            href="index.php?route=wishlist-toggle&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                            title="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>"
                            aria-label="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>">
                            &hearts;
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($annuncio['immagine_principale'])): ?>
                        <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                    <?php endif; ?>

                    <h3><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h3>
                    <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                    <p><?= e($annuncio['descrizione'] ?? '') ?></p>
                    <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <p>
                        <strong>Venditore:</strong>
                        <a href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                            <?= e($annuncio['venditore_username'] ?? '') ?>
                        </a>
                    </p>

                    <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business'])): ?>
                        <?php if ((int)($annuncio['id_utente'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)): ?>
                            <a class="btn btn-secondary" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <p>Nessun annuncio disponibile al momento.</p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>
