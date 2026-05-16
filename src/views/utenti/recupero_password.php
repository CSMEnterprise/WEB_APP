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

<div style="min-height:65vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
    <div class="card" style="max-width:440px;width:100%;padding:48px 40px;">

        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:44px;margin-bottom:16px;">🔑</div>
            <h1 style="font-size:26px;font-weight:800;margin-bottom:8px;">Password dimenticata?</h1>
            <p class="muted">Inserisci la tua email e ti invieremo un link per reimpostarla.</p>
        </div>

        <?php if (!empty($successo)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successo, ENT_QUOTES, 'UTF-8') ?></div>

            <?php if ($debugLink): ?>
                <div style="background:rgba(234,179,8,0.1);border:2px dashed rgba(234,179,8,0.4);border-radius:14px;padding:20px 24px;text-align:left;margin:20px 0;">
                    <p style="color:#fbbf24;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px;">
                        🛠 Modalità Debug — Link di reset
                    </p>
                    <p style="color:var(--muted);font-size:13px;margin-bottom:14px;">
                        L'email non viene inviata in modalità debug.<br>Clicca direttamente il link qui sotto:
                    </p>
                    <a href="<?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>"
                       style="display:block;word-break:break-all;background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.3);border-radius:10px;padding:12px 14px;color:#a78bfa;font-size:13px;font-weight:600;text-decoration:none;">
                        <?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </div>
            <?php endif; ?>

            <p style="text-align:center;margin-top:20px;">
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

                <button type="submit" class="btn" style="width:100%;text-align:center;margin-top:4px;">
                    Invia link di recupero
                </button>
            </form>

            <p style="text-align:center;margin-top:24px;">
                <a href="index.php?route=login" style="color:var(--muted);font-size:13px;">← Torna al login</a>
            </p>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
