<?php
$pageTitle = 'Registrazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione</h1>

    <form method="post" action="index.php?route=register-post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" name="telefono">

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
            title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
            required>

        <button class="btn" type="submit">Crea account</button>
    </form>

    <p>Hai già un account? <a href="index.php?route=login">Accedi</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>