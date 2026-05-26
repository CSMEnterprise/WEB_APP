<?php
$pageTitle = 'Profilo admin';
require __DIR__ . '/../layout/header.php';
?>

<h1>Profilo admin</h1>

<section class="grid">
    <article class="card">
        <h2>Utenti</h2>
        <p class="price"><?= e($stats['totUtenti'] ?? 0) ?></p>
    </article>

    <article class="card">
        <h2>Annunci</h2>
        <p class="price"><?= e($stats['totAnnunci'] ?? 0) ?></p>
    </article>

    <article class="card">
        <h2>Segnalazioni aperte</h2>
        <p class="price"><?= e($stats['totSegnalazioni'] ?? 0) ?></p>
    </article>

    <article class="card">
        <h2>Pagamenti</h2>
        <p class="price"><?= e($stats['totPagamenti'] ?? 0) ?></p>
    </article>
</section>

<p>
    <a class="btn" href="index.php?route=admin-utenti">Gestisci utenti</a>
    <a class="btn btn-secondary" href="index.php?route=admin-segnalazioni">Gestisci segnalazioni</a>
</p>

<section class="u-style-001">
    <h2>Azioni eseguite da te</h2>

    <?php if (!empty($azioniModera)): ?>
        <table>
            <thead>
                <tr>
                    <th>Azione</th>
                    <th>Riferimento</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($azioniModera as $azione): ?>
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
                        <td><?= e($azione['azione_compiuta'] ?? '') ?></td>
                        <td><?= e(!empty($riferimenti) ? implode(', ', $riferimenti) : '-') ?></td>
                        <td><?= e($azione['data_azione'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="card">
            <p>Non hai ancora azioni di moderazione registrate.</p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
