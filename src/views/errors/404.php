<?php
$pageTitle = 'Pagina non trovata';
require __DIR__ . '/../layout/header.php';
?>
<div class="empty-state">
    <h1>404 - Pagina non trovata</h1>
    <p>La pagina richiesta non esiste oppure non è più disponibile.</p>
    <a class="btn" href="index.php?action=annunci">Torna agli annunci</a>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
