<?php
$pageTitle = 'Esito pagamento';
require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h1>Esito pagamento</h1>

    <?php if (($status ?? '') === 'ok'): ?>
        <div class="alert alert-success">Pagamento completato correttamente!</div>
        <p>Vuoi lasciare un feedback al venditore?</p>
        <a class="btn" href="index.php?route=feedback-create&id_pagamento=<?= e($idPagamento ?? '') ?>">
            Lascia un feedback
        </a>
        <a class="btn btn-secondary" href="index.php?route=annunci">Torna agli annunci</a>
    <?php else: ?>
        <div class="alert alert-error">Pagamento non completato.</div>
        <a class="btn" href="index.php?route=annunci">Torna agli annunci</a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
