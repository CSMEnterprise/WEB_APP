<?php
$pageTitle = 'Checkout';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<h1>Checkout</h1>

<?php if (!empty($annuncio)): ?>
    <div class="card">
        <h2><?= e($annuncio['titolo'] ?? '') ?></h2>
        <p><?= e($annuncio['descrizione'] ?? '') ?></p>
        <p class="price">Totale: € <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>

        <form method="post" action="index.php">
            <input type="hidden" name="route" value="pagamento-conferma">
            <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? '') ?>">

            <label for="paypal_transaction_id">ID transazione PayPal</label>
            <input type="text" id="paypal_transaction_id" name="paypal_transaction_id" placeholder="Campo simulato per progetto universitario">

            <button class="btn" type="submit">Conferma pagamento</button>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-error">Pagamento non disponibile.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>