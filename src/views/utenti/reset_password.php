<?php
$pageTitle = 'Nuova password';
require __DIR__ . '/../layout/header.php';
$token = htmlspecialchars($_GET['token'] ?? ($_POST['token'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

<div style="min-height:65vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
    <div class="card" style="max-width:440px;width:100%;padding:48px 40px;">

        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:44px;margin-bottom:16px;">🔒</div>
            <h1 style="font-size:26px;font-weight:800;margin-bottom:8px;">Nuova password</h1>
            <p class="muted">Scegli una password sicura per il tuo account.</p>
        </div>

        <?php if (!empty($errore)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errore, ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (($idUtente ?? 0) === 0): ?>
                <p style="text-align:center;margin-top:16px;">
                    <a href="index.php?route=recupero-password" class="btn btn-secondary">Richiedi nuovo link</a>
                </p>
                <?php require __DIR__ . '/../layout/footer.php'; ?>
                <?php return; ?>
            <?php endif; ?>
        <?php endif; ?>

        <form method="post" action="index.php?route=reset-password-post">
            <input type="hidden" name="token" value="<?= $token ?>">

            <label for="newPassword">Nuova password</label>
            <div class="password-wrapper">
                <input type="password" id="newPassword" name="password"
                       pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                       title="Almeno 10 caratteri, una maiuscola e un carattere speciale."
                       autocomplete="new-password" required>
                <button class="btn btn-secondary btn-password-toggle" type="button"
                        onclick="togglePasswordVisibility('newPassword', this)">Mostra</button>
            </div>

            <label for="newPasswordConfirm">Conferma password</label>
            <div class="password-wrapper">
                <input type="password" id="newPasswordConfirm" name="password_confirm"
                       pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                       autocomplete="new-password" required>
                <button class="btn btn-secondary btn-password-toggle" type="button"
                        onclick="togglePasswordVisibility('newPasswordConfirm', this)">Mostra</button>
            </div>

            <button type="submit" class="btn" style="width:100%;text-align:center;margin-top:4px;">
                Salva nuova password
            </button>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
