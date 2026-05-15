<?php
$pageTitle = 'Registrazione utente';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione utente</h1>
    <p class="muted">Crea un account personale per acquistare, vendere e salvare annunci.</p>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-user-post">

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?= e($_POST['username'] ?? '') ?>"
            required>

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($_POST['email'] ?? '') ?>"
            required>

        <label for="telefono">Telefono</label>
        <input
            type="text"
            id="telefono"
            name="telefono"
            value="<?= e($_POST['telefono'] ?? '') ?>">

        <label for="userRegisterPassword">Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="userRegisterPassword"
                name="password"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
                required
                autocomplete="new-password">

            <button
                class="btn btn-secondary btn-password-toggle"
                type="button"
                onclick="togglePasswordVisibility('userRegisterPassword', this)">
                Mostra
            </button>
        </div>

        <label for="userRegisterPasswordConfirm">Conferma password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="userRegisterPasswordConfirm"
                name="password_confirm"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                title="Ripeti la stessa password scelta sopra."
                required
                autocomplete="new-password">

            <button
                class="btn btn-secondary btn-password-toggle"
                type="button"
                onclick="togglePasswordVisibility('userRegisterPasswordConfirm', this)">
                Mostra
            </button>
        </div>

        <button class="btn" type="submit">Crea account utente</button>
    </form>

    <p>
        Vuoi scegliere un altro tipo di account?
        <a href="index.php?route=register">Torna alla scelta registrazione</a>
    </p>
    <p>Hai già un account? <a href="index.php?route=login">Accedi</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
