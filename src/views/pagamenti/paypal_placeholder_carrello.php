<?php
$pageTitle = 'Pagamento PayPal';
require __DIR__ . '/../layout/header.php';
?>

<div class="paypal-page">
    <div class="card paypal-card">

        <span class="paypal-brand">PayPal</span>
        <p class="muted">Simulazione pagamento sandbox</p>

        <div class="paypal-summary">
            <h3 class="u-style-040">Riepilogo ordine</h3>

            <?php foreach ($items as $item): ?>
                <div class="u-style-041">
                    <div class="u-style-042">
                        <strong class="u-style-004"><?= e($item['titolo'] ?? '') ?></strong>
                        <p class="muted u-style-043">
                            <?php if (!empty($item['venditore_business_id'])): ?>
                                <?= e($item['venditore_nome_azienda'] ?? '') ?>
                                <span class="seller-pro-badge">PRO</span>
                            <?php else: ?>
                                <?= e($item['venditore_username'] ?? '') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <strong class="u-style-044">
                        € <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?>
                    </strong>
                </div>
            <?php endforeach; ?>

            <div class="u-style-045">
                <strong>Totale</strong>
                <span class="price u-style-046">
                    € <?= number_format((float)($totale ?? 0), 2, ',', '.') ?>
                </span>
            </div>

            <div class="u-style-047">
                <strong>Spedizione a:</strong><br>
                <?php
                    $via = trim(($indirizzoSpedizione['via'] ?? '') . ' ' . ($indirizzoSpedizione['numero'] ?? ''));
                    $loc = trim(($indirizzoSpedizione['cap'] ?? '') . ' ' . ($indirizzoSpedizione['citta'] ?? ''));
                    if (!empty($indirizzoSpedizione['provincia'])) {
                        $loc .= ' (' . $indirizzoSpedizione['provincia'] . ')';
                    }
                    echo e(implode(', ', array_filter([$via, $loc, $indirizzoSpedizione['paese'] ?? 'Italia'])));
                ?>
            </div>
        </div>

        <form method="post" action="index.php?route=pagamento-conferma-carrello" class="paypal-actions">
            <?php foreach ($items as $item): ?>
                <input type="hidden" name="id_annunci[]" value="<?= e($item['id_annuncio'] ?? '') ?>">
            <?php endforeach; ?>
            <input type="hidden" name="id_indirizzo" value="<?= e($indirizzoSpedizione['id_indirizzo'] ?? '') ?>">
            <input type="hidden" name="paypal_transaction_id" value="<?= e($paypalTransactionId ?? '') ?>">

            <button type="submit" class="btn paypal-confirm-btn">
                Conferma pagamento · € <?= number_format((float)($totale ?? 0), 2, ',', '.') ?>
            </button>
            <a class="btn btn-secondary" href="index.php?route=paypal-cancel">Annulla</a>
        </form>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
