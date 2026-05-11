<?php
$pageTitle = 'Richiesta non valida';
$message = $message ?? 'La richiesta non contiene tutti i dati necessari.';
require __DIR__ . '/../layout/header.php';
?>
<div class="empty-state">
    <h1>Richiesta non valida</h1>
    <p><?= e($message) ?></p>
    <a class="btn" href="index.php?action=annunci">Torna agli annunci</a>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
