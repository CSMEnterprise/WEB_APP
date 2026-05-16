<?php
$pageTitle = 'Verifica email';
require __DIR__ . '/../layout/header.php';
$email = htmlspecialchars($_GET['email'] ?? ($email ?? ''), ENT_QUOTES, 'UTF-8');

// Leggi eventuale link debug dalla sessione
$debugMail = $_SESSION['debug_mail'] ?? null;
$debugLink = ($debugMail && $debugMail['tipo'] === 'verifica') ? $debugMail['link'] : null;
// Pulisci dalla sessione dopo averlo letto
if ($debugLink) {
    unset($_SESSION['debug_mail']);
}
?>

<div style="min-height:65vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
    <div class="card" style="max-width:480px;width:100%;text-align:center;padding:48px 40px;">

        <div style="font-size:52px;margin-bottom:20px;">📧</div>
        <h1 style="font-size:26px;font-weight:800;margin-bottom:12px;">Controlla la tua email</h1>

        <?php if (!empty($successo)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successo, ENT_QUOTES, 'UTF-8') ?></div>
        <?php else: ?>
            <p class="muted" style="line-height:1.7;margin-bottom:8px;">
                Abbiamo inviato un link di verifica a
                <?php if ($email !== ''): ?>
                    <strong style="color:var(--text);"><?= $email ?></strong>
                <?php else: ?>
                    il tuo indirizzo email
                <?php endif; ?>.
            </p>
            <p class="muted" style="line-height:1.7;margin-bottom:28px;">
                Clicca il link nell'email per attivare il tuo account.<br>
                Il link scadrà tra <strong style="color:var(--text);">48 ore</strong>.
            </p>
        <?php endif; ?>

        <?php if ($debugLink): ?>
            <div style="background:rgba(234,179,8,0.1);border:2px dashed rgba(234,179,8,0.4);border-radius:14px;padding:20px 24px;text-align:left;margin-bottom:20px;">
                <p style="color:#fbbf24;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px;">
                    🛠 Modalità Debug — Link di verifica
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

        <details style="text-align:left;margin-top:16px;">
            <summary style="cursor:pointer;color:var(--muted);font-size:13px;margin-bottom:12px;">
                Non hai ricevuto l'email?
            </summary>
            <form method="post" action="index.php?route=reinvia-verifica" style="margin-top:12px;">
                <label for="reinvia_email">La tua email</label>
                <input type="email" id="reinvia_email" name="email"
                       value="<?= $email ?>" required
                       placeholder="tuaemail@esempio.it">
                <button type="submit" class="btn" style="width:100%;text-align:center;">
                    Reinvia email di verifica
                </button>
            </form>
        </details>

        <p style="margin-top:28px;">
            <a href="index.php?route=login" class="btn btn-secondary">Torna al login</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
