<?php
$pageTitle = 'Carrello';
$items = $items ?? [];
$totale = $totale ?? 0;
require __DIR__ . '/../layout/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Carrello</h1>
        <p class="muted">Riepilogo degli articoli selezionati.</p>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="empty-state">
        <h2>Il carrello è vuoto</h2>
        <p>Aggiungi un annuncio al carrello per procedere con l’acquisto.</p>
        <a class="btn" href="index.php?route=annunci">Vai agli annunci</a>
    </div>
<?php else: ?>
    <section class="cart-layout">
        <div class="card">
            <?php foreach ($items as $item): ?>
                <?php
                    $id = $item['id_annuncio'] ?? $item->id_annuncio ?? '';
                    $titolo = $item['titolo'] ?? $item->titolo ?? 'Annuncio';
                    $prezzo = $item['prezzo'] ?? $item->prezzo ?? 0;
                ?>
                <div class="cart-row">
                    <div>
                        <h2><?= e($titolo) ?></h2>
                        <p class="muted">ID annuncio: <?= e($id) ?></p>
                    </div>

                    <div class="cart-price">
                        <strong>€ <?= number_format((float)$prezzo, 2, ',', '.') ?></strong>
                        <a class="link-danger" href="index.php?route=carrello-remove&id=<?= e($id) ?>">Rimuovi</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="card summary">
            <h2>Totale</h2>
            <p class="price price-large">€ <?= number_format((float)$totale, 2, ',', '.') ?></p>
            <a class="btn full" href="index.php?route=checkout">Procedi al pagamento</a>
        </aside>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
