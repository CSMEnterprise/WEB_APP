<?php
$pageTitle = 'Gestione segnalazioni';
require __DIR__ . '/../layout/header.php';

$oggettoFilter = $filters['oggetto'] ?? '';
$tipologiaFilter = $filters['tipologia'] ?? '';
?>

<h1>Gestione segnalazioni</h1>

<section class="card">
    <h2>Filtri</h2>

    <form method="get" action="index.php">
        <input type="hidden" name="route" value="admin-segnalazioni">

        <label for="oggetto">Oggetto segnalato</label>
        <select id="oggetto" name="oggetto">
            <option value="">Tutti</option>
            <option value="annuncio" <?= $oggettoFilter === 'annuncio' ? 'selected' : '' ?>>Annuncio</option>
            <option value="utente" <?= $oggettoFilter === 'utente' ? 'selected' : '' ?>>Utente</option>
            <option value="business" <?= $oggettoFilter === 'business' ? 'selected' : '' ?>>Business</option>
            <option value="feedback" <?= $oggettoFilter === 'feedback' ? 'selected' : '' ?>>Feedback</option>
        </select>

        <label for="tipologia">Tipologia</label>
        <select id="tipologia" name="tipologia">
            <option value="">Tutte</option>
            <option value="Spam" <?= $tipologiaFilter === 'Spam' ? 'selected' : '' ?>>Spam</option>
            <option value="Truffa" <?= $tipologiaFilter === 'Truffa' ? 'selected' : '' ?>>Truffa</option>
            <option value="Contenuto_inappropriato" <?= $tipologiaFilter === 'Contenuto_inappropriato' ? 'selected' : '' ?>>Contenuto inappropriato</option>
            <option value="Altro" <?= $tipologiaFilter === 'Altro' ? 'selected' : '' ?>>Altro</option>
        </select>

        <button class="btn" type="submit">Filtra</button>
        <a class="btn btn-secondary" href="index.php?route=admin-segnalazioni">Reset</a>
    </form>
</section>

<?php if (!empty($segnalazioni)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Segnalante</th>
                <th>Tipologia</th>
                <th>Descrizione</th>
                <th>Oggetto segnalato</th>
                <th>Stato</th>
                <th>Data</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($segnalazioni as $s): ?>
                <?php
                if (!empty($s['id_annuncio'])) {
                    $oggettoLabel = '📦 Annuncio: ' . ($s['annuncio_titolo'] ?? '#' . $s['id_annuncio']);
                    $oggettoLink  = 'index.php?route=annuncio&id=' . (int) $s['id_annuncio'];
                } elseif (!empty($s['id_utente_segnalato'])) {
                    $oggettoLabel = '👤 Utente: ' . ($s['utente_segnalato_username'] ?? '#' . $s['id_utente_segnalato']);
                    $oggettoLink  = 'index.php?route=admin-utenti';
                } elseif (!empty($s['id_business'])) {
                    $oggettoLabel = '🏢 Business: ' . ($s['business_nome'] ?? '#' . $s['id_business']);
                    $oggettoLink  = null;
                } elseif (!empty($s['id_feedback'])) {
                    $oggettoLabel = '💬 Feedback #' . $s['feedback_id'];
                    $oggettoLink  = null;
                } else {
                    $oggettoLabel = '—';
                    $oggettoLink  = null;
                }
                ?>
                <tr>
                    <td><?= e($s['id_segnalazione'] ?? '') ?></td>
                    <td><?= e($s['segnalante_username'] ?? '') ?></td>
                    <td><?= e($s['tipologia'] ?? '') ?></td>
                    <td><?= e($s['descrizione'] ?? '—') ?></td>
                    <td>
                        <?= e($oggettoLabel) ?>
                        <?php if ($oggettoLink): ?>
                            <br>
                            <a class="btn btn-secondary" style="margin-top:6px;font-size:12px;padding:5px 10px;"
                               href="<?= e($oggettoLink) ?>" target="_blank">
                                Vai all'oggetto →
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?= e($s['stato'] ?? '') ?></td>
                    <td><?= e($s['data_segnalazione'] ?? '') ?></td>
                    <td>
                        <?php if (($s['stato'] ?? '') !== 'Risolta'): ?>
                            <a class="btn" href="index.php?route=segnalazione-close&id=<?= e($s['id_segnalazione']) ?>">Chiudi</a>
                        <?php endif; ?>
                        <a class="btn btn-danger" href="index.php?route=segnalazione-delete&id=<?= e($s['id_segnalazione']) ?>">Elimina</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nessuna segnalazione presente.</p>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
