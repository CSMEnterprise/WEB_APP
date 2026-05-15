<?php
$pageTitle = 'Registrazione utente';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione utente</h1>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?route=register-user-post">
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
            value="<?= e($_POST['telefono'] ?? '') ?>"
            required>

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
            title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
            required
            autocomplete="new-password">

        <button class="btn" type="submit">Crea account utente</button>
    </form>

    <p>
        Vuoi registrare un'azienda?
        <a href="index.php?route=register-business">Registrati come business</a>
    </p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
