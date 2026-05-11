<?php
$pageTitle = 'Nuova segnalazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Invia segnalazione</h1>

    <form method="post" action="index.php?route=segnalazione-store">
        <input type="hidden" name="id_annuncio" value="<?= e($_GET['id_annuncio'] ?? '') ?>">
        <input type="hidden" name="id_utente_segnalato" value="<?= e($_GET['id_utente_segnalato'] ?? '') ?>">
        <input type="hidden" name="id_business" value="<?= e($_GET['id_business'] ?? '') ?>">
        <input type="hidden" name="id_feedback" value="<?= e($_GET['id_feedback'] ?? '') ?>">

        <label for="tipologia">Tipologia</label>
        <select id="tipologia" name="tipologia" required>
            <option value="Spam">Spam</option>
            <option value="Truffa">Truffa</option>
            <option value="Contenuto_inappropriato">Contenuto inappropriato</option>
            <option value="Altro">Altro</option>
        </select>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione"></textarea>

        <button class="btn" type="submit">Invia segnalazione</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
