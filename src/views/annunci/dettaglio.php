<?php
$pageTitle = $annuncio['titolo'] ?? 'Dettaglio annuncio';
require __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($annuncio)): ?>
    <?php $isOwner = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && (int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0); ?>
    <?php $isBusiness = !empty($_SESSION['is_business']); ?>
    <?php $canUseWishlist = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && !$isBusiness && !$isOwner; ?>
    <?php $isInWishlist = $canUseWishlist && in_array((int)($annuncio['id_annuncio'] ?? 0), $wishlistIds ?? [], true); ?>

    <article class="card annuncio-card">
        <?php if ($canUseWishlist): ?>
            <a
                class="wishlist-heart <?= $isInWishlist ? 'wishlist-heart-active' : '' ?>"
                href="index.php?route=wishlist-toggle&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                title="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>"
                aria-label="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>">
                &hearts;
            </a>
        <?php endif; ?>

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
        <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?: 'Non specificato') ?></p>
        <p><strong>Stato vendita:</strong> <?= e(ucfirst((string)($annuncio['stato'] ?? ''))) ?></p>
        <?php $numeroFeedbackVenditore = count($feedbackVenditore ?? []); ?>
        <?php $stelleVenditore = (int) round((float)($mediaVenditore ?? 0)); ?>
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
            <?php if ($numeroFeedbackVenditore > 0): ?>
                <span style="display:inline-flex;align-items:center;gap:4px;margin-left:8px;color:#f59e0b;" title="<?= e(number_format((float)$mediaVenditore, 1)) ?> su 5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span><?= $i <= $stelleVenditore ? '&#9733;' : '&#9734;' ?></span>
                    <?php endfor; ?>
                    <strong style="color:var(--text);font-size:13px;"><?= e(number_format((float)$mediaVenditore, 1)) ?></strong>
                    <span class="muted" style="font-size:13px;">(<?= e((string)$numeroFeedbackVenditore) ?>)</span>
                </span>
            <?php else: ?>
                <span class="muted" style="margin-left:8px;font-size:13px;">Nessuna recensione</span>
            <?php endif; ?>
            <a class="btn btn-secondary"
               style="font-size:12px;padding:4px 10px;margin-left:10px;"
               href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                Vedi profilo
            </a>
        </p>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <div class="alert alert-success">Accesso admin: carrello, wishlist e acquisto sono disattivati.</div>
            <?php elseif ($isBusiness && !$isOwner): ?>
                <div class="alert alert-success">Account business: puoi vendere prodotti, ma carrello, wishlist e acquisto sono disattivati.</div>
                <a class="btn btn-secondary" href="index.php?route=segnalazione-create&id_annuncio=<?= e($annuncio['id_annuncio'] ?? '') ?>">Segnala</a>
            <?php elseif ($isOwner): ?>
                <div class="alert alert-success">Questo è un tuo annuncio: carrello e acquisto sono disattivati.</div>
                <a class="btn btn-danger" href="index.php?route=annuncio-delete&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Elimina</a>
            <?php else: ?>
                <a class="btn" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                <a class="btn btn-secondary" href="index.php?route=checkout&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Acquista</a>
                <a class="btn btn-secondary" href="index.php?route=segnalazione-create&id_annuncio=<?= e($annuncio['id_annuncio'] ?? '') ?>">Segnala</a>
            <?php endif; ?>
        <?php endif; ?>
    </article>
<?php else: ?>
    <div class="alert alert-error">Annuncio non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
