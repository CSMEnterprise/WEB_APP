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
        <input type="text" id="nome_azienda" name="nome_azienda" required>

        <label for="p_iva">Partita IVA</label>
        <input type="text" id="p_iva" name="p_iva" required>

        <label for="email_aziendale">Email aziendale</label>
        <input type="email" id="email_aziendale" name="email_aziendale" required>

        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" name="telefono">

        <fieldset>
            <legend>Indirizzo sede</legend>

            <label for="via">Via / Corso / Piazza</label>
            <input type="text" id="via" name="via">

            <label for="numero">Numero civico</label>
            <input type="text" id="numero" name="numero">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" maxlength="5">

            <label for="citta">Città</label>
            <input type="text" id="citta" name="citta">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2">
        </fieldset>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione"></textarea>

        <button class="btn" type="submit">Crea account business</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>