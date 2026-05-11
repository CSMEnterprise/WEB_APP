<?php
$pageTitle = 'Segnala contenuto';
require __DIR__ . '/../layout/header.php';
?>

<section class="card auth-card">
    <h1>Segnala contenuto</h1>
    <p class="muted">Usa questo modulo per segnalare annunci, utenti, business o feedback non corretti.</p>

    <form method="post" action="index.php?action=segnalazione_store" class="form">
        <input type="hidden" name="id_annuncio" value="<?= e($_GET['id_annuncio'] ?? '') ?>">
        <input type="hidden" name="id_utente_segnalato" value="<?= e($_GET['id_utente'] ?? '') ?>">
        <input type="hidden" name="id_business" value="<?= e($_GET['id_business'] ?? '') ?>">
        <input type="hidden" name="id_feedback" value="<?= e($_GET['id_feedback'] ?? '') ?>">

        <label>
            Tipologia
            <select name="tipologia" required>
                <option value="Spam">Spam</option>
                <option value="Truffa">Truffa</option>
                <option value="Contenuto_inappropriato">Contenuto inappropriato</option>
                <option value="Altro">Altro</option>
            </select>
        </label>

        <label>
            Descrizione
            <textarea name="descrizione" rows="5" placeholder="Descrivi brevemente il problema"></textarea>
        </label>

        <button class="btn" type="submit">Invia segnalazione</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
