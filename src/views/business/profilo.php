<?php
$pageTitle = 'Area business';
require __DIR__ . '/../layout/header.php';
?>

<h1>Area business</h1>

<?php if (!empty($business)): ?>
    <section class="card">
        <h2><?= e($business['nome_azienda'] ?? '') ?></h2>
        <p><strong>Partita IVA:</strong> <?= e($business['p_iva'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= e($business['email_aziendale'] ?? '') ?></p>
        <p><strong>Telefono:</strong> <?= e($business['telefono'] ?? '') ?></p>
        <p><strong>Verificato:</strong> <?= !empty($business['verificato']) ? 'Sì' : 'No' ?></p>
        <p><?= e($business['descrizione'] ?? '') ?></p>

        <?php if (!empty($business['via']) || !empty($business['citta'])): ?>
            <hr>
            <p><strong>Sede:</strong>
                <?= e(trim(
                    ($business['via']    ?? '') . ' ' . ($business['numero'] ?? '') . ', ' .
                    ($business['cap']    ?? '') . ' ' . ($business['citta']  ?? '') .
                    (!empty($business['provincia']) ? ' (' . $business['provincia'] . ')' : '')
                )) ?>
            </p>
        <?php endif; ?>
    </section>

    <p>
        <button type="button" class="btn btn-secondary" onclick="toggleIndirizzoForm()">
            <?= !empty($business['via']) ? 'Modifica indirizzo sede' : 'Aggiungi indirizzo sede' ?>
        </button>
    </p>

    <div id="indirizzoForm" class="card" style="display: none;">
        <h2>Indirizzo sede</h2>

        <form method="post" action="index.php?route=business-indirizzo-store">
            <label for="via">Via / Corso / Piazza</label>
            <input
                type="text"
                id="via"
                name="via"
                value="<?= e($business['via'] ?? '') ?>"
                required>

            <label for="numero">Numero civico</label>
            <input
                type="text"
                id="numero"
                name="numero"
                value="<?= e($business['numero'] ?? '') ?>">

            <label for="cap">CAP</label>
            <input
                type="text"
                id="cap"
                name="cap"
                maxlength="5"
                value="<?= e($business['cap'] ?? '') ?>">

            <label for="citta">Città</label>
            <input
                type="text"
                id="citta"
                name="citta"
                value="<?= e($business['citta'] ?? '') ?>"
                required>

            <label for="provincia">Provincia</label>
            <input
                type="text"
                id="provincia"
                name="provincia"
                maxlength="2"
                value="<?= e($business['provincia'] ?? '') ?>">

            <button type="submit" class="btn">Salva indirizzo</button>
        </form>
    </div>

    <h2>I miei annunci</h2>

    <?php if (!empty($annunci)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Prezzo</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($annunci as $annuncio): ?>
                    <tr>
                        <td><?= e($annuncio['titolo'] ?? '') ?></td>
                        <td>€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></td>
                        <td><?= e($annuncio['stato'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nessun annuncio pubblicato.</p>
    <?php endif; ?>

    <p>
        <a class="btn" href="index.php?route=business-ordini">Ordini ricevuti</a>
    </p>

    <script>
        function toggleIndirizzoForm() {
            const form = document.getElementById('indirizzoForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
<?php else: ?>
    <div class="card">
        <p>Non hai ancora un account business.</p>
        <a class="btn" href="index.php?route=business-create">Crea account business</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>