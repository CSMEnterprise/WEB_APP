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

        <?php if (!empty($utente['nome']) || !empty($utente['indirizzo'])): ?>
            <hr>
            <p><strong>Nome e cognome spedizione:</strong> <?= e($utente['nome'] ?? '') ?></p>
            <p><strong>Indirizzo di spedizione:</strong> <?= e($utente['indirizzo'] ?? '') ?></p>
        <?php endif; ?>
    </div>

    <p>
        <button type="button" class="btn" onclick="toggleIndirizzoForm()">
            Aggiungi indirizzo di spedizione
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

            <label for="indirizzo">Indirizzo di spedizione</label>
            <input
                type="text"
                id="indirizzo"
                name="indirizzo"
                value="<?= e($utente['indirizzo'] ?? '') ?>"
                required>

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