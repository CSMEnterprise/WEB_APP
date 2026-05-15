<?php
$pageTitle = 'Profilo';
require __DIR__ . '/../layout/header.php';
?>

<h1>Profilo utente</h1>

<?php if (!empty($utente)): ?>
    <div class="card">
        <p><strong>Username:</strong> <?= e($utente['username'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= e($utente['email'] ?? '') ?></p>
        <p><strong>Telefono:</strong> <?= e($utente['telefono'] ?? '') ?></p>

        <?php if (!empty($utente['via']) || !empty($utente['citta'])): ?>
            <hr>
            <p><strong>Nome spedizione:</strong> <?= e($utente['nome'] ?? '') ?></p>
            <p><strong>Indirizzo di spedizione:</strong>
                <?= e(trim(
                    ($utente['via'] ?? '') . ' ' . ($utente['numero'] ?? '') . ', ' .
                    ($utente['cap']  ?? '') . ' ' . ($utente['citta']  ?? '') .
                    (!empty($utente['provincia']) ? ' (' . $utente['provincia'] . ')' : '')
                )) ?>
            </p>
        <?php endif; ?>
    </div>

    <p>
        <button type="button" class="btn" onclick="toggleIndirizzoForm()">
            <?= (!empty($utente['via'])) ? 'Modifica indirizzo di spedizione' : 'Aggiungi indirizzo di spedizione' ?>
        </button>

        <a class="btn" href="index.php?route=annuncio-create">Crea annuncio</a>
        <a class="btn btn-secondary" href="index.php?route=feedback">I miei feedback</a>
    </p>

    <div id="indirizzoForm" class="card" style="display: none;">
        <h2>Indirizzo di spedizione</h2>

        <form method="post" action="index.php?route=profilo-indirizzo-store">
            <label for="nome">Nome e cognome</label>
            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= e($utente['nome'] ?? '') ?>"
                required>

            <label for="via">Via / Corso / Piazza</label>
            <input
                type="text"
                id="via"
                name="via"
                value="<?= e($utente['via'] ?? '') ?>"
                required>

            <label for="numero">Numero civico</label>
            <input
                type="text"
                id="numero"
                name="numero"
                value="<?= e($utente['numero'] ?? '') ?>">

            <label for="cap">CAP</label>
            <input
                type="text"
                id="cap"
                name="cap"
                maxlength="5"
                value="<?= e($utente['cap'] ?? '') ?>">

            <label for="citta">Città</label>
            <input
                type="text"
                id="citta"
                name="citta"
                value="<?= e($utente['citta'] ?? '') ?>"
                required>

            <label for="provincia">Provincia</label>
            <input
                type="text"
                id="provincia"
                name="provincia"
                maxlength="2"
                value="<?= e($utente['provincia'] ?? '') ?>">

            <button type="submit" class="btn">Salva indirizzo</button>
        </form>
    </div>

    <script>
        function toggleIndirizzoForm() {
            const form = document.getElementById('indirizzoForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
<?php else: ?>
    <div class="alert alert-error">Utente non trovato.</div>
<?php endif; ?>