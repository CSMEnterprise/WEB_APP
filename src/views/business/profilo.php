<?php
$pageTitle = 'Profilo business';
$business = $business ?? null;
require __DIR__ . '/../layout/header.php';

if (!$business):
?>
    <div class="empty-state">
        <h1>Nessun account business</h1>
        <p>Puoi creare un account business per pubblicare annunci come azienda.</p>
        <a class="btn" href="index.php?action=business_create">Crea account business</a>
    </div>
<?php
require __DIR__ . '/../layout/footer.php';
return;
endif;
?>

<section class="card">
    <p class="badge"><?= !empty($business['verificato'] ?? $business->verificato ?? false) ? 'Verificato' : 'In attesa di verifica' ?></p>
    <h1><?= e($business['nome_azienda'] ?? $business->nome_azienda ?? '') ?></h1>
    <p><?= nl2br(e($business['descrizione'] ?? $business->descrizione ?? '')) ?></p>

    <dl class="info-list">
        <dt>Partita IVA</dt>
        <dd><?= e($business['p_iva'] ?? $business->p_iva ?? '') ?></dd>

        <dt>Email aziendale</dt>
        <dd><?= e($business['email_aziendale'] ?? $business->email_aziendale ?? '') ?></dd>

        <dt>Telefono</dt>
        <dd><?= e($business['telefono'] ?? $business->telefono ?? '-') ?></dd>

        <dt>Indirizzo</dt>
        <dd><?= e($business['indirizzo'] ?? $business->indirizzo ?? '-') ?></dd>
    </dl>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
