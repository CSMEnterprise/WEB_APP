<?php
$pageTitle = 'Nuova password';
require __DIR__ . '/../layout/header.php';
$token = htmlspecialchars($_GET['token'] ?? ($_POST['token'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

<div class="u-style-080">
    <div class="card u-style-081">

        <div class="u-style-082">
            <div class="u-style-083">🔒</div>
            <h1 class="u-style-084">Nuova password</h1>
            <p class="muted">Scegli una password sicura per il tuo account.</p>
        </div>

        <?php if (!empty($errore)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errore, ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (($idUtente ?? 0) === 0): ?>
                <p class="u-style-125">
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

            <button type="submit" class="btn u-style-089">
                Salva nuova password
            </button>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
