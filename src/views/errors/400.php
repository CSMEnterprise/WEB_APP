<?php
$pageTitle = 'Errore';
require __DIR__ . '/../layout/header.php';
?>

<div class="alert alert-error">
    <?= e($errore ?? 'Richiesta non valida.') ?>
</div>

<p>
    <a class="btn" href="index.php?route=home">Torna alla home</a>
</p>

<?php require __DIR__ . '/../layout/footer.php'; ?>
