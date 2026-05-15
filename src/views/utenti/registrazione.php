<?php
$pageTitle = 'Registrazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione</h1>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" name="telefono">

        <label for="registerPassword">Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="registerPassword"
                name="password"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
                required>

            <button
                class="btn btn-password-toggle"
                type="button"
                onclick="togglePasswordVisibility('registerPassword', this)">
                Mostra
            </button>
        </div>

        <button class="btn" type="submit">Crea account</button>
    </form>

    <p>Hai già un account? <a href="index.php?route=login">Accedi</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>