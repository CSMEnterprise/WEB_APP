<?php
$pageTitle = 'Esito pagamento';
$success = $success ?? true;
require __DIR__ . '/../layout/header.php';
?>

<section class="empty-state">
    <?php if ($success): ?>
        <h1>Pagamento completato</h1>
        <p>Il tuo acquisto è stato registrato correttamente.</p>
    <?php else: ?>
        <h1>Pagamento non riuscito</h1>
        <p>Si è verificato un problema durante il pagamento.</p>
    <?php endif; ?>

    <a class="btn" href="index.php?action=annunci">Torna agli annunci</a>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
