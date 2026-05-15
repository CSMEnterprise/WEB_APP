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
            pattern="[A-Za-z0-9_.-]{3,30}"
            minlength="3"
            maxlength="30"
            title="Usa 3-30 caratteri: lettere, numeri, punto, trattino o underscore."
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
            value="<?= e($_POST['telefono'] ?? '') ?>"
            pattern="\+?[0-9 ]{8,15}"
            title="Inserisci 8-15 cifre; puoi iniziare con +."
            required>

        <label for="businessPassword">Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="businessPassword"
                name="password"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                autocomplete="new-password"
                title="La password deve contenere almeno 10 caratteri, una lettera maiuscola e un carattere speciale."
                required>

            <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPassword', this)">
                Mostra
            </button>
        </div>

        <label for="businessPasswordConfirm">Conferma password</label>
        <div class="password-wrapper">
            <input
                type="password"
                id="businessPasswordConfirm"
                name="password_confirm"
                pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                autocomplete="new-password"
                title="Ripeti la stessa password scelta sopra."
                required>

            <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPasswordConfirm', this)">
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
            pattern="[\p{L}0-9 .&'-]{2,80}"
            minlength="2"
            maxlength="80"
            title="Inserisci 2-80 caratteri validi."
            required>

        <label for="p_iva">Partita IVA</label>
        <input
            type="text"
            id="p_iva"
            name="p_iva"
            value="<?= e($_POST['p_iva'] ?? '') ?>"
            pattern="[0-9]{11}"
            maxlength="11"
            inputmode="numeric"
            title="La partita IVA deve contenere esattamente 11 cifre."
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
            <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" title="Il CAP deve contenere 5 cifre." value="<?= e($_POST['cap'] ?? '') ?>">

            <label for="citta">Città</label>
            <input type="text" id="citta" name="citta" pattern="[\p{L} .'-]{2,80}" title="Inserisci 2-80 caratteri validi." value="<?= e($_POST['citta'] ?? '') ?>">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{2}" title="Inserisci 2 lettere, ad esempio TO." value="<?= e($_POST['provincia'] ?? '') ?>">
        </fieldset>

        <button class="btn" type="submit">Crea account business</button>
    </form>

    <p>Vuoi creare un account normale? <a href="index.php?route=register">Registrati come utente</a></p>
    <p>Hai già un account? <a href="index.php?route=login">Accedi</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
