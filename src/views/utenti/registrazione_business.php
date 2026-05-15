<?php
$pageTitle = 'Registrazione business';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione business</h1>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-business-post">

        <h2>Dati account</h2>

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?= e($_POST['username'] ?? '') ?>"
            required>

        <label for="email">Email accesso</label>
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

        <label for="businessPassword">Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="businessPassword"
                name="password"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
                required>

            <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPassword', this)">
                Mostra
            </button>
        </div>

        <hr>

        <h2>Dati business</h2>

        <label for="nome_azienda">Nome azienda</label>
        <input
            type="text"
            id="nome_azienda"
            name="nome_azienda"
            value="<?= e($_POST['nome_azienda'] ?? '') ?>"
            required>

        <label for="p_iva">Partita IVA</label>
        <input
            type="text"
            id="p_iva"
            name="p_iva"
            value="<?= e($_POST['p_iva'] ?? '') ?>"
            required>

        <label for="email_aziendale">Email aziendale</label>
        <input
            type="email"
            id="email_aziendale"
            name="email_aziendale"
            value="<?= e($_POST['email_aziendale'] ?? '') ?>"
            required>

        <fieldset>
            <legend>Indirizzo sede</legend>

            <label for="via">Via / Corso / Piazza</label>
            <input type="text" id="via" name="via" value="<?= e($_POST['via'] ?? '') ?>">

            <label for="numero">Numero civico</label>
            <input type="text" id="numero" name="numero" value="<?= e($_POST['numero'] ?? '') ?>">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" maxlength="5" value="<?= e($_POST['cap'] ?? '') ?>">

            <label for="citta">Città</label>
            <input type="text" id="citta" name="citta" value="<?= e($_POST['citta'] ?? '') ?>">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" value="<?= e($_POST['provincia'] ?? '') ?>">
        </fieldset>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione"><?= e($_POST['descrizione'] ?? '') ?></textarea>

        <button class="btn" type="submit">Crea account business</button>
    </form>

    <p>Vuoi creare un account normale? <a href="index.php?route=register">Registrati come utente</a></p>
    <p>Hai già un account? <a href="index.php?route=login">Accedi</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
