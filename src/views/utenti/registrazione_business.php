<?php
$pageTitle = 'Registrazione business';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Registrazione business</h1>
    <p class="muted">
        Inserisci i dati aziendali, il referente e le credenziali.
        L'email aziendale verrà usata anche per il login.
    </p>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?route=register-business-post">
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
            maxlength="20"
            value="<?= e($_POST['p_iva'] ?? '') ?>"
            required>

        <fieldset>
            <legend>Indirizzo sede</legend>

            <label for="via">Via / Corso / Piazza</label>
            <input
                type="text"
                id="via"
                name="via"
                value="<?= e($_POST['via'] ?? '') ?>">

            <label for="numero">Numero civico</label>
            <input
                type="text"
                id="numero"
                name="numero"
                value="<?= e($_POST['numero'] ?? '') ?>">

            <label for="cap">CAP</label>
            <input
                type="text"
                id="cap"
                name="cap"
                maxlength="5"
                value="<?= e($_POST['cap'] ?? '') ?>">

            <label for="citta">Città</label>
            <input
                type="text"
                id="citta"
                name="citta"
                value="<?= e($_POST['citta'] ?? '') ?>">

            <label for="provincia">Provincia</label>
            <input
                type="text"
                id="provincia"
                name="provincia"
                maxlength="2"
                value="<?= e($_POST['provincia'] ?? '') ?>">
        </fieldset>

        <label for="descrizione">Descrizione azienda</label>
        <textarea id="descrizione" name="descrizione"><?= e($_POST['descrizione'] ?? '') ?></textarea>

        <label for="nome">Nome e cognome referente</label>
        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= e($_POST['nome'] ?? '') ?>"
            required>

        <label for="telefono">Telefono</label>
        <input
            type="text"
            id="telefono"
            name="telefono"
            value="<?= e($_POST['telefono'] ?? '') ?>"
            required>

        <label for="email_aziendale">Email aziendale</label>
        <input
            type="email"
            id="email_aziendale"
            name="email_aziendale"
            value="<?= e($_POST['email_aziendale'] ?? '') ?>"
            required
            autocomplete="email">

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
            title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
            required
            autocomplete="new-password">

        <button class="btn" type="submit">Crea account business</button>
    </form>

    <p>
        Vuoi un account personale?
        <a href="index.php?route=register-user">Registrati come utente</a>
    </p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
