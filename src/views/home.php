<?php
$pageTitle = 'Home';
require __DIR__ . '/layout/header.php';
?>

<?php
$q = trim($_GET['q'] ?? '');
$idCategoria = (int)($_GET['id_categoria'] ?? 0);
$isRicerca = $q !== '' || $idCategoria > 0;
?>

<?php if (!$isRicerca && !isset($_SESSION['user_id'])): ?>
<section class="card">
    <h1>Compra e vendi articoli nerd in modo semplice.</h1>
    <p>
        NerdVault è un marketplace per videogiochi, fumetti, action figure, carte collezionabili,
        gadget e prodotti da collezione.
    </p>
    <p>
        <a class="btn btn-secondary" href="index.php?route=register">Crea account</a>
    </p>
</section>
<?php endif; ?>

<?php if ($q !== '' && !empty($utenti)): ?>
<section style="margin-bottom: 32px;">
    <h2>Utenti trovati</h2>
    <div class="grid">
        <?php foreach ($utenti as $u): ?>
            <div class="card clickable-card"
                 data-href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>"
                 role="link" tabindex="0"
                 style="display:flex;align-items:center;gap:14px;">
                <div style="width:54px;height:54px;border-radius:50%;overflow:hidden;background:var(--bg-input);
                            display:flex;align-items:center;justify-content:center;
                            border:1px solid var(--border);flex:0 0 54px;">
                    <?php if (!empty($u['propic'])): ?>
                        <img src="<?= e($u['propic']) ?>" alt="Foto profilo" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <span style="font-size:26px;">&#128100;</span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong><?= e($u['username'] ?? '') ?></strong>
                    <?php if (!empty($u['nome'])): ?>
                        <p class="muted" style="margin:2px 0;"><?= e($u['nome']) ?></p>
                    <?php endif; ?>
                    <a class="btn btn-secondary"
                       style="font-size:12px;padding:5px 10px;margin-top:6px;display:inline-block;"
                       href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>">
                        Vedi profilo
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section style="margin-top: 28px;">
    <div class="nav" style="align-items:flex-start;">
        <h2><?= e($homeTitoloAnnunci ?? 'Annunci in evidenza') ?></h2>
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
                            <span class="seller-name-line">
                                <?= e(!empty($annuncio['venditore_business_id']) ? ($annuncio['venditore_nome_azienda'] ?? '') : ($annuncio['venditore_username'] ?? '')) ?>
                                <?php if (!empty($annuncio['venditore_business_id'])): ?>
                                    <span class="seller-pro-badge">PRO</span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </p>

                    <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business'])): ?>
                        <?php if ((int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)): ?>
                            <p class="muted" style="margin:4px 0 0;">È un tuo annuncio.</p>
                        <?php elseif (in_array((int)($annuncio['id_annuncio'] ?? 0), $carrelloIds ?? [], true)): ?>
                            <span class="btn btn-secondary" style="opacity:.55;cursor:default;pointer-events:none;">✓ Nel carrello</span>
                        <?php else: ?>
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
