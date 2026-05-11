<?php
$pageTitle = 'Dashboard admin';
$stats = $stats ?? [];
$segnalazioni = $segnalazioni ?? [];
$businessDaVerificare = $businessDaVerificare ?? [];
require __DIR__ . '/../layout/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Dashboard admin</h1>
        <p class="muted">Area di controllo per moderazione e verifiche.</p>
    </div>
</div>

<section class="stats-grid">
    <div class="card stat">
        <span>Utenti</span>
        <strong><?= e($stats['utenti'] ?? 0) ?></strong>
    </div>
    <div class="card stat">
        <span>Annunci</span>
        <strong><?= e($stats['annunci'] ?? 0) ?></strong>
    </div>
    <div class="card stat">
        <span>Segnalazioni aperte</span>
        <strong><?= e($stats['segnalazioni_aperte'] ?? 0) ?></strong>
    </div>
</section>

<section class="grid-2">
    <div class="card">
        <h2>Segnalazioni recenti</h2>

        <?php if (empty($segnalazioni)): ?>
            <p class="muted">Nessuna segnalazione da mostrare.</p>
        <?php else: ?>
            <?php foreach ($segnalazioni as $s): ?>
                <div class="list-row">
                    <strong><?= e($s['tipologia'] ?? $s->tipologia ?? '') ?></strong>
                    <span><?= e($s['stato'] ?? $s->stato ?? '') ?></span>
                    <a href="index.php?route=admin-segnalazione&id=<?= e($s['id_segnalazione'] ?? $s->id_segnalazione ?? '') ?>">Apri</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Business da verificare</h2>

        <?php if (empty($businessDaVerificare)): ?>
            <p class="muted">Nessuna richiesta in attesa.</p>
        <?php else: ?>
            <?php foreach ($businessDaVerificare as $b): ?>
                <div class="list-row">
                    <strong><?= e($b['nome_azienda'] ?? $b->nome_azienda ?? '') ?></strong>
                    <a href="index.php?route=admin-business&id=<?= e($b['id_acc_business'] ?? $b->id_acc_business ?? '') ?>">Verifica</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
