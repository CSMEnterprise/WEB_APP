<?php
$pageTitle = 'Pagamento PayPal simulato';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<section class="paypal-page">
    <div class="paypal-card">
        <div class="paypal-brand">PayPal</div>
        <h1>Pagamento simulato</h1>

        <p class="muted">
            Questa schermata è un placeholder per simulare il pagamento PayPal.
            Nessun pagamento reale verrà effettuato.
        </p>

        <?php if (!empty($annuncio)): ?>
            <div class="paypal-summary">
                <h2>Riepilogo ordine</h2>

                <?php if (!empty($annuncio['immagine_principale'])): ?>
                    <img class="paypal-product-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                <?php endif; ?>

                <p><strong>Annuncio:</strong> <?= e($annuncio['titolo'] ?? '') ?></p>
                <p>
                    <strong>Venditore:</strong>
                    <a href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                        <?= e($annuncio['venditore_username'] ?? '') ?>
                    </a>
                </p>
                <p class="price">Totale: € <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>
            </div>

            <form method="post" action="index.php?route=pagamento-conferma" class="paypal-actions">
                <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? '') ?>">
                <input type="hidden" name="paypal_transaction_id" value="<?= e($paypalTransactionId ?? '') ?>">

                <button class="btn paypal-confirm-btn" type="submit">
                    Paga con PayPal simulato
                </button>

                <a class="btn btn-secondary" href="index.php?route=paypal-cancel">
                    Annulla
                </a>
            </form>
        <?php else: ?>
            <div class="alert alert-error">Pagamento non disponibile.</div>
            <a class="btn" href="index.php?route=carrello">Torna al carrello</a>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
