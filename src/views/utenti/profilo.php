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

        <form method="post" action="index.php">
            <input type="hidden" name="route" value="profilo-indirizzo-store">
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

    <section class="profile-annunci">
        <?php
            $filtroAnnunci = $filtroAnnunci ?? 'attivo';
            $titoloAnnunciProfilo = $titoloAnnunciProfilo ?? 'Annunci attivi';
            $isAttivi = $filtroAnnunci === 'attivo';
            $isVenduti = $filtroAnnunci === 'venduto';
        ?>

        <div class="nav" style="align-items:flex-start; gap: 12px; flex-wrap: wrap;">
            <h2><?= e($titoloAnnunciProfilo) ?></h2>
            <a class="btn" href="index.php?route=annuncio-create">Nuovo annuncio</a>
        </div>

        <div class="card" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <strong>Mostra:</strong>
            <a
                class="btn <?= $isAttivi ? '' : 'btn-secondary' ?>"
                href="index.php?route=profilo-annunci-attivi">
                Annunci attivi
            </a>
            <a
                class="btn <?= $isVenduti ? '' : 'btn-secondary' ?>"
                href="index.php?route=profilo-annunci-venduti">
                Annunci venduti
            </a>
        </div>

        <?php if (!empty($annunciUtente)): ?>
            <div class="grid">
                <?php foreach ($annunciUtente as $annuncio): ?>
                    <article class="card">
                        <?php if (!empty($annuncio['immagine_principale'])): ?>
                            <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                        <?php endif; ?>

                        <h3><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h3>
                        <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                        <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                        <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?? '') ?></p>
                        <p><strong>Stato vendita:</strong> <?= e($annuncio['stato'] ?? '') ?></p>

                        <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                        <?php if ($isAttivi): ?>
                            <a class="btn btn-danger" href="index.php?route=annuncio-delete&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Elimina</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <?php if ($isVenduti): ?>
                    <p>Non hai ancora annunci venduti.</p>
                <?php else: ?>
                    <p>Non hai annunci attivi.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <script>
        function toggleIndirizzoForm() {
            const form = document.getElementById('indirizzoForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
<?php else: ?>
    <div class="alert alert-error">Utente non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
