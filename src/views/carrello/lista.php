<?php
$pageTitle = 'Carrello';
require __DIR__ . '/../layout/header.php';
?>

<h1>Carrello</h1>

<?php if (!empty($carrello)): ?>
    <?php
        $purchasableItems = array_filter($carrello, static function ($item) {
            $isOwner = (int)($item['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
            return !$isOwner && ($item['stato'] ?? '') === 'attivo';
        });
    ?>

    <section class="cart-layout">
        <div class="cart-items">
            <?php foreach ($carrello as $item): ?>
                <?php $isOwner = (int)($item['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0); ?>

                <article
                    class="card clickable-card annuncio-card cart-item-card"
                    data-href="index.php?route=annuncio&id=<?= e($item['id_annuncio'] ?? '') ?>"
                    role="link"
                    tabindex="0">
                    <?php if (!empty($item['immagine_principale'])): ?>
                        <img class="annuncio-card-img" src="<?= e($item['immagine_principale']) ?>" alt="Foto annuncio">
                    <?php endif; ?>

                    <h2><?= e($item['titolo'] ?? '') ?></h2>
                    <p class="price">€ <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <p>
                        <strong>Venditore:</strong>
                        <a href="index.php?route=venditore&id=<?= e($item['id_utente'] ?? '') ?>">
                            <span class="seller-name-line">
                                <?= e(!empty($item['venditore_business_id']) ? ($item['venditore_nome_azienda'] ?? '') : ($item['venditore_username'] ?? '')) ?>
                                <?php if (!empty($item['venditore_business_id'])): ?>
                                    <span class="seller-pro-badge">PRO</span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </p>
                    <p><strong>Stato vendita:</strong> <?= e(ucfirst((string)($item['stato'] ?? ''))) ?></p>

                    <div class="cart-item-actions">
                        <a class="btn btn-danger" href="index.php?route=carrello-remove&id=<?= e($item['id_annuncio'] ?? '') ?>">Rimuovi</a>
                        <a class="btn btn-secondary" href="index.php?route=annuncio&id=<?= e($item['id_annuncio'] ?? '') ?>">Dettagli</a>
                    </div>

                    <?php if ($isOwner): ?>
                        <p class="muted">Non puoi acquistare un tuo annuncio.</p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="card cart-summary">
            <h2>Totale</h2>
            <p class="price">€ <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>

            <div class="cart-summary-actions">
                <?php if (!empty($purchasableItems)): ?>
                    <a class="btn" href="index.php?route=checkout-carrello">
                        Procedi all'acquisto (<?= count($purchasableItems) ?> <?= count($purchasableItems) === 1 ? 'articolo' : 'articoli' ?>)
                    </a>
                <?php endif; ?>

                <a class="btn btn-danger" href="index.php?route=carrello-clear">Svuota carrello</a>
            </div>
        </aside>
    </section>
<?php else: ?>
    <div class="card">
        <p>Il carrello è vuoto.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
