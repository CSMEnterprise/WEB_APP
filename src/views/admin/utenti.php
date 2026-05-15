<?php
$pageTitle = 'Gestione utenti';
require __DIR__ . '/../layout/header.php';

$searchUtente = $filters['q_utente'] ?? '';
?>

<h1>Gestione utenti</h1>

<?php if ((int)($_SESSION['livello_sicurezza'] ?? 1) === 2): ?>
    <section style="margin-bottom: 32px;">
        <h2>Gestione admin</h2>

        <?php if (!empty($admins)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Livello</th>
                        <th>Stato</th>
                        <th>Data creazione</th>
                        <th>Azione</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <?php
                        $isCurrentAdmin = (int)($admin['id_admin'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
                        $isAdminLivello1 = (int)($admin['livello_sicurezza'] ?? 1) === 1;
                        ?>
                        <tr>
                            <td><?= e($admin['id_admin'] ?? '') ?></td>
                            <td><?= e($admin['email'] ?? '') ?></td>
                            <td><?= e($admin['livello_sicurezza'] ?? '') ?></td>
                            <td><?= !empty($admin['stato_ban']) ? 'Bannato' : 'Attivo' ?></td>
                            <td><?= e($admin['data_creazione'] ?? '') ?></td>
                            <td>
                                <?php if ($isCurrentAdmin): ?>
                                    <span class="muted">Account corrente</span>
                                <?php elseif (!$isAdminLivello1): ?>
                                    <span class="muted">Non moderabile</span>
                                <?php elseif (!empty($admin['stato_ban'])): ?>
                                    <a class="btn" href="index.php?route=admin-sblocca-admin&id=<?= e($admin['id_admin'] ?? '') ?>">Sblocca</a>
                                <?php else: ?>
                                    <a class="btn btn-danger" href="index.php?route=admin-banna-admin&id=<?= e($admin['id_admin'] ?? '') ?>">Banna</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nessun admin trovato.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>

<h2>Utenti registrati</h2>

<section class="card">
    <h3>Ricerca utenti registrati</h3>

    <form method="get" action="index.php">
        <input type="hidden" name="route" value="admin-utenti">

        <label for="q_utente">Utente</label>
        <input
            type="search"
            id="q_utente"
            name="q_utente"
            placeholder="Cerca per ID, username, email, nome o telefono"
            value="<?= e($searchUtente) ?>">

        <button class="btn" type="submit">Cerca</button>
        <a class="btn btn-secondary" href="index.php?route=admin-utenti">Reset</a>
    </form>
</section>

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
