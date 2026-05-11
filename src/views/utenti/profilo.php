<?php
$pageTitle = 'Profilo';
$utente = $utente ?? [];
require __DIR__ . '/../layout/header.php';
?>

<section class="profile-layout">
    <div class="card profile-card">
        <div class="avatar">
            <?php if (!empty($utente['propic'] ?? $utente->propic ?? '')): ?>
                <img src="<?= e($utente['propic'] ?? $utente->propic) ?>" alt="Foto profilo">
            <?php else: ?>
                <span><?= e(strtoupper(substr($utente['username'] ?? $utente->username ?? 'U', 0, 1))) ?></span>
            <?php endif; ?>
        </div>

        <h1><?= e($utente['username'] ?? $utente->username ?? 'Utente') ?></h1>
        <p class="muted"><?= e($utente['email'] ?? $utente->email ?? '') ?></p>
    </div>

    <div class="card">
        <h2>Dati personali</h2>

        <dl class="info-list">
            <dt>Nome</dt>
            <dd><?= e($utente['nome'] ?? $utente->nome ?? '-') ?></dd>

            <dt>Telefono</dt>
            <dd><?= e($utente['telefono'] ?? $utente->telefono ?? '-') ?></dd>

            <dt>Indirizzo</dt>
            <dd><?= e($utente['indirizzo'] ?? $utente->indirizzo ?? '-') ?></dd>

            <dt>Registrazione</dt>
            <dd><?= e($utente['data_registrazione'] ?? $utente->data_registrazione ?? '-') ?></dd>
        </dl>

        <div class="actions">
            <a class="btn" href="index.php?action=annunci-miei">I miei annunci</a>
            <a class="btn btn-secondary" href="index.php?action=business_create">Account business</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
