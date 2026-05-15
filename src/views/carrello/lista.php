<?php
$pageTitle = 'Carrello';
require __DIR__ . '/../layout/header.php';
?>

<h1>Carrello</h1>

<?php if (!empty($carrello)): ?>
    <section class="grid">
        <div>
            <?php foreach ($carrello as $item): ?>
                <?php $isOwner = (int)($item['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0); ?>

                <div class="card">
                    <?php if (!empty($item['immagine_principale'])): ?>
                        <img class="annuncio-card-img" src="<?= e($item['immagine_principale']) ?>" alt="Foto annuncio">
                    <?php endif; ?>

                    <h2><?= e($item['titolo'] ?? '') ?></h2>
                    <p class="muted">ID annuncio: <?= e($item['id_annuncio'] ?? '') ?></p>
                    <p class="price">€ <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <p><strong>Venditore:</strong> <?= e($item['venditore_username'] ?? '') ?></p>
                    <p><strong>Stato vendita:</strong> <?= e($item['stato'] ?? '') ?></p>

                    <a class="btn btn-danger" href="index.php?route=carrello-remove&id=<?= e($item['id_annuncio'] ?? '') ?>">Rimuovi</a>
                    <a class="btn btn-secondary" href="index.php?route=annuncio&id=<?= e($item['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if ($isOwner): ?>
                        <p class="muted">Non puoi acquistare un tuo annuncio.</p>
                    <?php elseif (($item['stato'] ?? '') === 'attivo'): ?>
                        <a class="btn" href="index.php?route=checkout&id=<?= e($item['id_annuncio'] ?? '') ?>">Acquista</a>
                    <?php else: ?>
                        <p class="muted">Annuncio non acquistabile.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="card">
            <h2>Totale</h2>
            <p class="price">€ <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>
            <a class="btn btn-danger" href="index.php?route=carrello-clear">Svuota carrello</a>
        </aside>
    </section>
<?php else: ?>
    <div class="card">
        <p>Il carrello è vuoto.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
