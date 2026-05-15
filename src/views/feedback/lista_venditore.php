<?php
$pageTitle = 'Feedback venditore';
require __DIR__ . '/../layout/header.php';
?>

<h1>Feedback ricevuti</h1>

<?php if (isset($media) && $media > 0): ?>
    <div class="card" style="display:flex; align-items:center; gap:16px;">
        <span style="font-size:36px; color:#f59e0b;">★</span>
        <div>
            <strong style="font-size:24px;"><?= number_format($media, 1) ?> / 5</strong>
            <p class="muted" style="margin:0;"><?= count($feedback ?? []) ?> recensioni</p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($feedback)): ?>
    <?php foreach ($feedback as $item): ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <strong><?= e($item['autore'] ?? '') ?></strong>
                <span style="color:#f59e0b; font-size:20px;">
                    <?= str_repeat('★', (int)($item['valutazione'] ?? 0)) ?>
                    <?= str_repeat('☆', 5 - (int)($item['valutazione'] ?? 0)) ?>
                </span>
            </div>
            <p class="muted" style="font-size:13px;">
                Annuncio: <a href="index.php?route=annuncio&id=<?= e($item['annuncio_id']) ?>"><?= e($item['annuncio_titolo'] ?? '') ?></a>
            </p>
            <?php if (!empty($item['commento'])): ?>
                <p><?= e($item['commento']) ?></p>
            <?php endif; ?>
            <p class="muted" style="font-size:12px;"><?= e($item['data_feedback'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card">
        <p>Nessun feedback ricevuto.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
