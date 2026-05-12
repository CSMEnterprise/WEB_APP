<?php
$pageTitle = 'Profilo';
require __DIR__ . '/../layout/header.php';
?>

<h1>Profilo utente</h1>

<?php if (!empty($utente)): ?>
    <div class="card">
        <p><strong>Username:</strong> <?= e($utente['username'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= e($utente['email'] ?? '') ?></p>
        <p><strong>Nome:</strong> <?= e($utente['nome'] ?? '') ?></p>
        <p><strong>Telefono:</strong> <?= e($utente['telefono'] ?? '') ?></p>
        <p><strong>Indirizzo:</strong> <?= e($utente['indirizzo'] ?? '') ?></p>
        <p><strong>Registrazione:</strong> <?= e($utente['data_registrazione'] ?? '') ?></p>
    </div>

    <p>
        <a class="btn" href="index.php?route=annuncio-create">Crea annuncio</a>
        <a class="btn btn-secondary" href="index.php?route=feedback">I miei feedback</a>
    </p>
<?php else: ?>
    <div class="alert alert-error">Utente non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
