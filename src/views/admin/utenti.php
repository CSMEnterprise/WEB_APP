<?php
$pageTitle = 'Gestione utenti';
require __DIR__ . '/../layout/header.php';
?>

<h1>Gestione utenti</h1>

<?php if (!empty($utenti)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Stato</th>
                <th>Azione</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utenti as $utente): ?>
                <tr>
                    <td><?= e($utente['id_utente'] ?? '') ?></td>
                    <td><?= e($utente['username'] ?? '') ?></td>
                    <td><?= e($utente['email'] ?? '') ?></td>
                    <td><?= !empty($utente['stato_ban']) ? 'Bannato' : 'Attivo' ?></td>
                    <td>
                        <?php if (!empty($utente['stato_ban'])): ?>
                            <a class="btn" href="index.php?route=admin-sblocca-utente&id=<?= e($utente['id_utente'] ?? '') ?>">Sblocca</a>
                        <?php else: ?>
                            <a class="btn btn-danger" href="index.php?route=admin-banna-utente&id=<?= e($utente['id_utente'] ?? '') ?>">Banna</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nessun utente trovato.</p>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
