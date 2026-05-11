<?php
$pageTitle = 'Pagina non trovata';
require __DIR__ . '/../layout/header.php';
?>

<div class="card">
    <h1>404 - Pagina non trovata</h1>
    <p>La risorsa richiesta non esiste oppure non è più disponibile.</p>
    <a class="btn" href="index.php?route=home">Torna alla home</a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
