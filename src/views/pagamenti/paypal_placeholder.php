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
            Questa schermata e un placeholder per simulare il pagamento PayPal.
            Nessun pagamento reale verra effettuato.
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
                        <span class="seller-name-line">
                            <?= e(!empty($annuncio['venditore_business_id']) ? ($annuncio['venditore_nome_azienda'] ?? '') : ($annuncio['venditore_username'] ?? '')) ?>
                            <?php if (!empty($annuncio['venditore_business_id'])): ?>
                                <span class="seller-pro-badge">PRO</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </p>
                <p class="price">Totale: &euro; <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>

                <?php if (!empty($indirizzoSpedizione)): ?>
                    <?php
                        $viaIndirizzo = trim(($indirizzoSpedizione['via'] ?? '') . ' ' . ($indirizzoSpedizione['numero'] ?? ''));
                        $localitaIndirizzo = trim(($indirizzoSpedizione['cap'] ?? '') . ' ' . ($indirizzoSpedizione['citta'] ?? ''));

                        if (!empty($indirizzoSpedizione['provincia'])) {
                            $localitaIndirizzo = trim($localitaIndirizzo . ' (' . $indirizzoSpedizione['provincia'] . ')');
                        }
                    ?>
                    <p><strong>Spedizione:</strong> <?= e(implode(', ', array_filter([$viaIndirizzo, $localitaIndirizzo, $indirizzoSpedizione['paese'] ?? 'Italia']))) ?></p>
                <?php endif; ?>
            </div>

            <form method="post" action="index.php?route=pagamento-conferma" class="paypal-actions">
                <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? '') ?>">
                <input type="hidden" name="id_indirizzo" value="<?= e($indirizzoSpedizione['id_indirizzo'] ?? '') ?>">
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
