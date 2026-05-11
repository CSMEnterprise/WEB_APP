<?php
$pageTitle = 'Gestione segnalazioni';
require __DIR__ . '/../layout/header.php';
?>

<h1>Gestione segnalazioni</h1>

<?php if (!empty($segnalazioni)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Segnalante</th>
                <th>Tipologia</th>
                <th>Descrizione</th>
                <th>Stato</th>
                <th>Data</th>
                <th>Azione</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($segnalazioni as $segnalazione): ?>
                <tr>
                    <td><?= e($segnalazione['id_segnalazione'] ?? '') ?></td>
                    <td><?= e($segnalazione['segnalante_username'] ?? '') ?></td>
                    <td><?= e($segnalazione['tipologia'] ?? '') ?></td>
                    <td><?= e($segnalazione['descrizione'] ?? '') ?></td>
                    <td><?= e($segnalazione['stato'] ?? '') ?></td>
                    <td><?= e($segnalazione['data_segnalazione'] ?? '') ?></td>
                    <td>
                        <a class="btn" href="index.php?route=segnalazione-close&id=<?= e($segnalazione['id_segnalazione'] ?? '') ?>">Chiudi</a>
                        <a class="btn btn-danger" href="index.php?route=segnalazione-delete&id=<?= e($segnalazione['id_segnalazione'] ?? '') ?>">Elimina</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nessuna segnalazione presente.</p>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
