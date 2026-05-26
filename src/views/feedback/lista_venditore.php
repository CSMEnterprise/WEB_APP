<?php
$pageTitle = 'Feedback venditore';
require __DIR__ . '/../layout/header.php';
?>

<h1>Feedback ricevuti</h1>

<?php if (isset($media) && $media > 0): ?>
    <div class="card u-style-016">
        <span class="u-style-017">★</span>
        <div>
            <strong class="u-style-018"><?= number_format($media, 1) ?> / 5</strong>
            <p class="muted u-style-019"><?= count($feedback ?? []) ?> recensioni</p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($feedback)): ?>
    <?php foreach ($feedback as $item): ?>
        <div class="card">
            <div class="u-style-020">
                <strong><?= e($item['autore'] ?? '') ?></strong>
                <span class="u-style-021">
                    <?= str_repeat('★', (int)($item['valutazione'] ?? 0)) ?>
                    <?= str_repeat('☆', 5 - (int)($item['valutazione'] ?? 0)) ?>
                </span>
            </div>
            <p class="muted u-style-004">
                Annuncio: <a href="index.php?route=annuncio&id=<?= e($item['annuncio_id']) ?>"><?= e($item['annuncio_titolo'] ?? '') ?></a>
            </p>
            <?php if (!empty($item['commento'])): ?>
                <p><?= e($item['commento']) ?></p>
            <?php endif; ?>
            <p class="muted u-style-022"><?= e($item['data_feedback'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card">
        <p>Nessun feedback ricevuto.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
