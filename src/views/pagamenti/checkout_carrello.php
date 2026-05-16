<?php
$pageTitle = 'Checkout';
require __DIR__ . '/../layout/header.php';
?>

<h1>Checkout</h1>

<section class="cart-layout">

    <!-- Lista articoli -->
    <div>
        <?php foreach ($items as $item): ?>
            <article class="card" style="display:flex;gap:16px;align-items:flex-start;margin-bottom:12px;">
                <?php if (!empty($item['immagine_principale'])): ?>
                    <img src="<?= e($item['immagine_principale']) ?>"
                         alt="Foto"
                         style="width:72px;height:72px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                <?php endif; ?>
                <div>
                    <strong><?= e($item['titolo'] ?? '') ?></strong>
                    <p class="price" style="margin:4px 0;font-size:16px;">
                        € <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?>
                    </p>
                    <p class="muted" style="margin:0;">
                        Venditore:
                        <?php if (!empty($item['venditore_business_id'])): ?>
                            <?= e($item['venditore_nome_azienda'] ?? '') ?>
                            <span class="seller-pro-badge">PRO</span>
                        <?php else: ?>
                            <?= e($item['venditore_username'] ?? '') ?>
                        <?php endif; ?>
                    </p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Riepilogo + indirizzo -->
    <aside class="card cart-summary">
        <h2>Totale</h2>
        <p class="price">€ <?= number_format((float)($totale ?? 0), 2, ',', '.') ?></p>
        <p class="muted"><?= count($items) ?> <?= count($items) === 1 ? 'articolo' : 'articoli' ?></p>

        <h2 style="margin-top:20px;">Indirizzo di spedizione</h2>

        <?php if (!empty($indirizziUtente)): ?>
            <form method="get" action="index.php" class="cart-summary-actions">
                <input type="hidden" name="route" value="paypal-placeholder-carrello">

                <?php foreach ($indirizziUtente as $indirizzo): ?>
                    <?php
                        $via = trim(($indirizzo['via'] ?? '') . ' ' . ($indirizzo['numero'] ?? ''));
                        $loc = trim(($indirizzo['cap'] ?? '') . ' ' . ($indirizzo['citta'] ?? ''));
                        if (!empty($indirizzo['provincia'])) {
                            $loc .= ' (' . $indirizzo['provincia'] . ')';
                        }
                        $label = implode(', ', array_filter([$via, $loc, $indirizzo['paese'] ?? 'Italia']));
                    ?>
                    <label style="display:flex;align-items:flex-start;gap:10px;text-transform:none;
                                  letter-spacing:0;color:var(--text);font-size:14px;
                                  border:1px solid var(--border);border-radius:14px;padding:12px;
                                  background:rgba(255,255,255,.03);cursor:pointer;">
                        <input
                            type="radio"
                            name="id_indirizzo"
                            value="<?= e($indirizzo['id_indirizzo'] ?? '') ?>"
                            <?= !empty($indirizzo['predefinito']) ? 'checked' : '' ?>
                            required
                            style="width:auto;margin:3px 0 0;">
                        <span>
                            <?= e($label) ?>
                            <?php if (!empty($indirizzo['predefinito'])): ?>
                                <span class="seller-pro-badge" style="margin-left:6px;">Predefinito</span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>

                <button class="btn" type="submit">Continua con PayPal</button>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                Aggiungi un indirizzo di spedizione prima di procedere al pagamento.
            </div>
            <a class="btn" href="index.php?route=profilo">Vai al profilo</a>
        <?php endif; ?>
    </aside>

</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
