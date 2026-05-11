<?php
$pageTitle = 'Login';
require __DIR__ . '/../layout/header.php';
?>

<section class="card auth-card">
    <h1>Accedi</h1>
    <p class="muted">Inserisci le tue credenziali per entrare nel marketplace.</p>

    <form method="post" action="index.php?route=login" class="form">
        <label>
            Email
            <input type="email" name="email" required autocomplete="email">
        </label>

        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button class="btn" type="submit">Accedi</button>
    </form>

    <p class="muted">
        Non hai ancora un account?
        <a href="index.php?route=register">Registrati</a>
    </p>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
