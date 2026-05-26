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

<div class="u-style-080">
    <div class="card u-style-130">

        <div class="u-style-127">📧</div>
        <h1 class="u-style-128">Controlla la tua email</h1>

        <?php if (!empty($successo)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successo, ENT_QUOTES, 'UTF-8') ?></div>
        <?php else: ?>
            <p class="muted u-style-131">
                Abbiamo inviato un link di verifica a
                <?php if ($email !== ''): ?>
                    <strong class="u-style-132"><?= $email ?></strong>
                <?php else: ?>
                    il tuo indirizzo email
                <?php endif; ?>.
            </p>
            <p class="muted u-style-133">
                Clicca il link nell'email per attivare il tuo account.<br>
                Il link scadrà tra <strong class="u-style-132">48 ore</strong>.
            </p>
        <?php endif; ?>

        <?php if ($debugLink): ?>
            <div class="u-style-134">
                <p class="u-style-086">
                    🛠 Modalità Debug — Link di verifica
                </p>
                <p class="u-style-087">
                    L'email non viene inviata in modalità debug.<br>Clicca direttamente il link qui sotto:
                </p>
                <a class="u-debug-link" href="<?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($debugLink, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
        <?php endif; ?>

        <details class="u-style-135">
            <summary class="u-style-136">
                Non hai ricevuto l'email?
            </summary>
            <form class="u-style-137" method="post" action="index.php?route=reinvia-verifica">
                <label for="reinvia_email">La tua email</label>
                <input type="email" id="reinvia_email" name="email"
                       value="<?= $email ?>" required
                       placeholder="tuaemail@esempio.it">
                <button type="submit" class="btn u-style-138">
                    Reinvia email di verifica
                </button>
            </form>
        </details>

        <p class="u-style-139">
            <a href="index.php?route=login" class="btn btn-secondary">Torna al login</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
