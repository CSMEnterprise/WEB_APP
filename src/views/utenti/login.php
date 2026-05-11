<?php
$pageTitle = 'Login';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Login</h1>

    <form method="post" action="index.php?route=login-post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button class="btn" type="submit">Accedi</button>
    </form>

    <p>Non hai un account? <a href="index.php?route=register">Registrati</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
