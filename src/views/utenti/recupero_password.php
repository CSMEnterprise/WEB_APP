<?php
$pageTitle = 'Recupero password';
require __DIR__ . '/../layout/header.php';

// Leggi eventuale link debug dalla sessione
$debugMail = $_SESSION['debug_mail'] ?? null;
$debugLink = ($debugMail && $debugMail['tipo'] === 'reset') ? $debugMail['link'] : null;
if ($debugLink) {
    unset($_SESSION['debug_mail']);
}
?>

<div class="u-style-080">
    <div class="card u-style-081">

        <div class="u-style-082">
            <div class="u-style-083">🔑</div>
            <h1 class="u-style-084">Password dimenticata?</h1>
            <p class="muted">Inserisci la tua email e ti invieremo un link per reimpostarla.</p>
        </div>

        <?php if (!empty($successo)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successo, ENT_QUOTES, 'UTF-8') ?></div>

            <?php if ($debugLink): ?>
                <div class="u-style-085">
                    <p class="u-style-086">
                        🛠 Modalità Debug — Link di reset
                    </p>
                    <p class="u-style-087">
                        L'email non viene inviata in modalità debug.<br>Clicca direttamente il link qui sotto:
                    </p>
                    <a class="u-debug-link" href="<?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </div>
            <?php endif; ?>

            <p class="u-style-088">
                <a href="index.php?route=login" class="btn btn-secondary">Torna al login</a>
            </p>
        <?php else: ?>
            <?php if (!empty($errore)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errore, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" action="index.php?route=recupero-password-post">
                <label for="email">La tua email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="tuaemail@esempio.it" required>

                <button type="submit" class="btn u-style-089">
                    Invia link di recupero
                </button>
            </form>

            <p class="u-style-090">
                <a class="u-style-091" href="index.php?route=login">← Torna al login</a>
            </p>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
