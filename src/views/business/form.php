<?php
$pageTitle = 'Crea account business';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Crea account business</h1>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="business-store">
        <label for="nome_azienda">Nome azienda</label>
        <input type="text" id="nome_azienda" name="nome_azienda" pattern="[\p{L}0-9 .&'-]{2,80}" minlength="2" maxlength="80" title="Inserisci 2-80 caratteri validi." required>

        <label for="p_iva">Partita IVA</label>
        <input type="text" id="p_iva" name="p_iva" pattern="[0-9]{11}" maxlength="11" inputmode="numeric" title="La partita IVA deve contenere esattamente 11 cifre." required>

        <label for="email_aziendale">Email aziendale</label>
        <input type="email" id="email_aziendale" name="email_aziendale" required>

        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" name="telefono" pattern="\+?[0-9 ]{8,15}" title="Inserisci 8-15 cifre; puoi iniziare con +.">

        <fieldset>
            <legend>Indirizzo sede</legend>

            <label for="via">Via / Corso / Piazza</label>
            <input type="text" id="via" name="via">

            <label for="numero">Numero civico</label>
            <input type="text" id="numero" name="numero">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" title="Il CAP deve contenere 5 cifre.">

            <label for="citta">Città</label>
            <input type="text" id="citta" name="citta" pattern="[\p{L} .'-]{2,80}" title="Inserisci 2-80 caratteri validi.">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{2}" title="Inserisci 2 lettere, ad esempio TO.">
        </fieldset>

        <button class="btn" type="submit">Crea account business</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
