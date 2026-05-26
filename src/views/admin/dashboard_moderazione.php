<?php
$pageTitle = 'Dashboard moderazione';
require __DIR__ . '/../layout/header.php';

$adminFilter = $filters['admin'] ?? '';
?>

<h1>Dashboard moderazione</h1>

<section class="card">
    <h2>Ricerca e filtri</h2>

    <form method="get" action="index.php">
        <input type="hidden" name="route" value="admin-dashboard">

        <label for="admin">Admin</label>
        <input
            type="search"
            id="admin"
            name="admin"
            placeholder="Cerca per email o ID admin"
            value="<?= e($adminFilter) ?>">

        <button class="btn" type="submit">Filtra</button>
        <a class="btn btn-secondary" href="index.php?route=admin-dashboard">Reset</a>
    </form>
</section>

<section class="u-style-001">
    <h2>Azioni di moderazione</h2>

    <?php if (!empty($azioniModerazione)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Livello</th>
                    <th>Azione</th>
                    <th>Riferimenti</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($azioniModerazione as $azione): ?>
                    <?php
                    $riferimenti = [];

                    if (!empty($azione['id_utente'])) {
                        $riferimenti[] = 'Utente #' . $azione['id_utente'];
                    }

                    if (!empty($azione['id_feedback'])) {
                        $riferimenti[] = 'Feedback #' . $azione['id_feedback'];
                    }

                    if (!empty($azione['id_annuncio'])) {
                        $riferimenti[] = 'Annuncio #' . $azione['id_annuncio'];
                    }

                    if (!empty($azione['id_business'])) {
                        $riferimenti[] = 'Business #' . $azione['id_business'];
                    }
                    ?>
                    <tr>
                        <td><?= e($azione['id_moderazione'] ?? '') ?></td>
                        <td>
                            #<?= e($azione['id_admin'] ?? '') ?><br>
                            <span class="muted"><?= e($azione['admin_email'] ?? '') ?></span>
                        </td>
                        <td><?= e($azione['livello_sicurezza'] ?? '') ?></td>
                        <td><?= e($azione['azione_compiuta'] ?? '') ?></td>
                        <td><?= e(!empty($riferimenti) ? implode(', ', $riferimenti) : '-') ?></td>
                        <td><?= e($azione['data_azione'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="card">
            <p>Nessuna azione di moderazione trovata.</p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
