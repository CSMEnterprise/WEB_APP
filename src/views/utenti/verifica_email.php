<?php
$pageTitle = 'Verifica email';
require __DIR__ . '/../layout/header.php';
?>

<div style="min-height:65vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
    <div class="card" style="max-width:460px;width:100%;text-align:center;padding:48px 40px;">

        <?php if (!empty($successo)): ?>
            <div style="font-size:52px;margin-bottom:20px;">✅</div>
            <h1 style="font-size:26px;font-weight:800;margin-bottom:12px;">Email verificata!</h1>
            <p class="muted" style="margin-bottom:28px;">Il tuo account è ora attivo. Puoi accedere.</p>
            <a href="index.php?route=login" class="btn">Vai al login</a>
        <?php else: ?>
            <div style="font-size:52px;margin-bottom:20px;">❌</div>
            <h1 style="font-size:26px;font-weight:800;margin-bottom:12px;">Verifica fallita</h1>
            <div class="alert alert-error"><?= htmlspecialchars($errore ?? 'Errore sconosciuto.', ENT_QUOTES, 'UTF-8') ?></div>
            <p style="margin-top:20px;">
                <a href="index.php?route=verifica-email-attesa" class="btn btn-secondary">Reinvia email</a>
                <a href="index.php?route=login" class="btn btn-secondary">Login</a>
            </p>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
