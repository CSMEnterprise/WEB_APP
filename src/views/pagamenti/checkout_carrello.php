<?php
$pageTitle = 'Checkout';
require __DIR__ . '/../layout/header.php';
?>

<h1>Checkout</h1>

<section class="cart-layout">

    <!-- Lista articoli -->
    <div>
        <?php foreach ($items as $item): ?>
            <article class="card u-style-036">
                <?php if (!empty($item['immagine_principale'])): ?>
                    <img class="u-checkout-thumb" src="<?= e($item['immagine_principale']) ?>"
                         alt="Foto">
                <?php endif; ?>
                <div>
                    <strong><?= e($item['titolo'] ?? '') ?></strong>
                    <p class="price u-style-037">
                        € <?= number_format((float)($item['prezzo'] ?? 0), 2, ',', '.') ?>
                    </p>
                    <p class="muted u-style-019">
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

        <h2 class="u-style-038">Indirizzo di spedizione</h2>

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
                    <label class="u-style-039">
                        <input
                            type="radio"
                            name="id_indirizzo"
                            value="<?= e($indirizzo['id_indirizzo'] ?? '') ?>"
                            <?= !empty($indirizzo['predefinito']) ? 'checked' : '' ?>
                            required
                            class="u-radio-compact">
                        <span>
                            <?= e($label) ?>
                            <?php if (!empty($indirizzo['predefinito'])): ?>
                                <span class="seller-pro-badge u-style-035">Predefinito</span>
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
