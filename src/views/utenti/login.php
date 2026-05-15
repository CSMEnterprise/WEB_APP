<?php
$pageTitle = 'Login';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Login</h1>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="login-post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="loginPassword">Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="loginPassword"
                name="password"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
                required>

            <button
                class="btn btn-password-toggle"
                type="button"
                onclick="togglePasswordVisibility('loginPassword', this)">
                Mostra
            </button>
        </div>

        <button class="btn" type="submit">Accedi</button>
    </form>

    <p>Non hai un account? <a href="index.php?route=register">Registrati</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>