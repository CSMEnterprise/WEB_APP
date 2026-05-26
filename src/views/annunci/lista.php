<?php
$pageTitle = 'Annunci';
require __DIR__ . '/../layout/header.php';

$q = trim($_GET['q'] ?? '');
$idCategoria = (int)($_GET['id_categoria'] ?? 0);
?>

<div class="u-style-007">
    <h1 class="u-style-008"><?= ($q !== '' || $idCategoria > 0) ? 'Risultati ricerca' : 'Annunci disponibili' ?></h1>

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
    <section class="u-style-002">
        <h2>Utenti trovati</h2>
        <div class="grid">
            <?php foreach ($utenti as $u): ?>
                <div
                    class="card clickable-card u-user-result-card"
                    data-href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>"
                    role="link"
                    tabindex="0">
                    <div class="u-style-009">
                        <?php if (!empty($u['propic'])): ?>
                            <img class="u-fill-image" src="<?= e($u['propic']) ?>" alt="Foto profilo">
                        <?php else: ?>
                            <span class="u-style-010">&#128100;</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?= e($u['username'] ?? '') ?></strong>
                        <?php if (!empty($u['nome'])): ?>
                            <p class="muted u-style-011"><?= e($u['nome']) ?></p>
                        <?php endif; ?>
                        <a class="btn btn-secondary u-small-profile-link"
                           href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>">
                            Vedi profilo
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

                    <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && (int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)): ?>
                        <?php if (($annuncio['stato'] ?? '') === 'attivo'): ?>
                            <a class="btn btn-secondary" href="index.php?route=annuncio-edit&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Modifica</a>
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
