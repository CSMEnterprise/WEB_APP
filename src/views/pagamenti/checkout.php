<?php
$pageTitle = 'Pagamento';
$annuncio = $annuncio ?? null;
$totale = $totale ?? ($annuncio['prezzo'] ?? $annuncio->prezzo ?? 0);
require __DIR__ . '/../layout/header.php';
?>

<section class="card auth-card">
    <h1>Conferma pagamento</h1>
    <p class="muted">Controlla il riepilogo prima di procedere.</p>

    <div class="summary-line">
        <span>Importo totale</span>
        <strong>€ <?= number_format((float)$totale, 2, ',', '.') ?></strong>
    </div>

    <form method="post" action="index.php?action=pagamento" class="form">
        <?php if ($annuncio): ?>
            <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? $annuncio->id_annuncio ?? '') ?>">
        <?php endif; ?>

        <button class="btn full" type="submit">Conferma pagamento</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
