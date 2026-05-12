<?php
$pageTitle = 'Segnalazioni';
require __DIR__ . '/../layout/header.php';
?>

<h1>Segnalazioni</h1>

<?php if (!empty($segnalazioni)): ?>
    <?php foreach ($segnalazioni as $segnalazione): ?>
        <div class="card">
            <h2><?= e($segnalazione['tipologia'] ?? '') ?></h2>
            <p><?= e($segnalazione['descrizione'] ?? '') ?></p>
            <p><strong>Stato:</strong> <?= e($segnalazione['stato'] ?? '') ?></p>
            <p class="muted"><?= e($segnalazione['data_segnalazione'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nessuna segnalazione presente.</p>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
