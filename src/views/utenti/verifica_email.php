<?php
$pageTitle = 'Verifica email';
require __DIR__ . '/../layout/header.php';
?>

<div class="u-style-080">
    <div class="card u-style-126">

        <?php if (!empty($successo)): ?>
            <div class="u-style-127">✅</div>
            <h1 class="u-style-128">Email verificata!</h1>
            <p class="muted u-style-129">Il tuo account è ora attivo. Puoi accedere.</p>
            <a href="index.php?route=login" class="btn">Vai al login</a>
        <?php else: ?>
            <div class="u-style-127">❌</div>
            <h1 class="u-style-128">Verifica fallita</h1>
            <div class="alert alert-error"><?= htmlspecialchars($errore ?? 'Errore sconosciuto.', ENT_QUOTES, 'UTF-8') ?></div>
            <p class="u-style-038">
                <a href="index.php?route=verifica-email-attesa" class="btn btn-secondary">Reinvia email</a>
                <a href="index.php?route=login" class="btn btn-secondary">Login</a>
            </p>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
