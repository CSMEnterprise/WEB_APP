<?php
$pageTitle = 'Feedback';
require __DIR__ . '/../layout/header.php';
?>

<h1>Feedback</h1>

<?php if (!empty($feedback)): ?>
    <?php foreach ($feedback as $item): ?>
        <div class="card">
            <h2><?= e($item['titolo'] ?? 'Feedback') ?></h2>
            <p><strong>Autore:</strong> <?= e($item['autore'] ?? '') ?></p>
            <p><strong>Valutazione:</strong> <?= e($item['valutazione'] ?? '') ?>/5</p>
            <p><?= e($item['commento'] ?? '') ?></p>
            <p class="muted"><?= e($item['data_feedback'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Non sono presenti feedback.</p>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
