<?php
$pageTitle = 'Esito pagamento';
require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h1>Esito pagamento</h1>

    <?php if (($status ?? '') === 'ok'): ?>
        <div class="alert alert-success">Pagamento completato correttamente.</div>
    <?php else: ?>
        <div class="alert alert-error">Pagamento non completato.</div>
    <?php endif; ?>

    <a class="btn" href="index.php?route=annunci">Torna agli annunci</a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
