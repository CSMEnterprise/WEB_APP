<?php
$pageTitle = 'Pagamento PayPal';
require __DIR__ . '/../layout/header.php';
?>

<div class="paypal-page">
    <div class="card paypal-card">

        <span class="paypal-brand">PayPal</span>
        <p class="muted">Simulazione pagamento sandbox</p>

        <div class="paypal-summary">
            <h3 style="margin:0 0 12px;">Riepilogo ordine</h3>

            <?php foreach ($items as $item): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;
                            padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06);gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <strong style="font-size:13px;"><?= e($item['titolo'] ?? '') ?></strong>
                        <p class="muted" style="margin:2px 0;font-size:12px;">
                            <?php if (!empty($item['venditore_business_id'])): ?>
                                <?= e($item['venditore_nome_azienda'] ?? '') ?>
                                <span class="seller-pro-badge">PRO</span>
                            <?php else: ?>
                                <?= e($item['venditore_username'] ?? '') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <strong style="white-space:nowrap;">
                        € <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?>
                    </strong>
                </div>
            <?php endforeach; ?>

            <div style="display:flex;justify-content:space-between;margin-top:14px;">
                <strong>Totale</strong>
                <span class="price" style="font-size:18px;">
                    € <?= number_format((float)($totale ?? 0), 2, ',', '.') ?>
                </span>
            </div>

            <div style="margin-top:12px;font-size:13px;color:var(--muted);">
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
