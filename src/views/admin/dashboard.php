<?php
$pageTitle = 'Admin dashboard';
require __DIR__ . '/../layout/header.php';
?>

<h1>Dashboard admin</h1>

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

<?php require __DIR__ . '/../layout/footer.php'; ?>
