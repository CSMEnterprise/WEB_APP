<?php
$pageTitle = 'Carrello';
require __DIR__ . '/../layout/header.php';
?>

<h1>Carrello</h1>

<?php if (!empty($carrello)): ?>
    <section class="grid">
        <div>
            <?php foreach ($carrello as $item): ?>
                <div class="card">
                    <h2><?= e($item['titolo'] ?? '') ?></h2>
                    <p class="muted">ID annuncio: <?= e($item['id_annuncio'] ?? '') ?></p>
                    <p class="price">€ <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <a class="btn btn-danger" href="index.php?route=carrello-remove&id=<?= e($item['id_annuncio'] ?? '') ?>">Rimuovi</a>
                    <a class="btn" href="index.php?route=checkout&id=<?= e($item['id_annuncio'] ?? '') ?>">Acquista</a>
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
