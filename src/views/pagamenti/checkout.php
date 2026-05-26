<?php
$pageTitle = 'Checkout';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<h1>Checkout</h1>

<?php if (!empty($annuncio)): ?>
    <section class="cart-layout">
        <div class="card">
            <h2><?= e($annuncio['titolo'] ?? '') ?></h2>
            <p><?= e($annuncio['descrizione'] ?? '') ?></p>
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
        </div>

        <aside class="card cart-summary">
            <h2>Indirizzo di spedizione</h2>

            <?php if (!empty($indirizziUtente)): ?>
                <form method="post" action="index.php?route=paypal-placeholder" class="cart-summary-actions">
                    <input type="hidden" name="id_annuncio" value="<?= e($annuncio['id_annuncio'] ?? '') ?>">

                    <?php foreach ($indirizziUtente as $indirizzo): ?>
                        <?php
                            $viaIndirizzo = trim(($indirizzo['via'] ?? '') . ' ' . ($indirizzo['numero'] ?? ''));
                            $localitaIndirizzo = trim(($indirizzo['cap'] ?? '') . ' ' . ($indirizzo['citta'] ?? ''));

                            if (!empty($indirizzo['provincia'])) {
                                $localitaIndirizzo = trim($localitaIndirizzo . ' (' . $indirizzo['provincia'] . ')');
                            }

                            $indirizzoLabel = implode(', ', array_filter([$viaIndirizzo, $localitaIndirizzo, $indirizzo['paese'] ?? 'Italia']));
                        ?>
                        <label class="u-style-034">
                            <input
                                type="radio"
                                name="id_indirizzo"
                                value="<?= e($indirizzo['id_indirizzo'] ?? '') ?>"
                                <?= !empty($indirizzo['predefinito']) ? 'checked' : '' ?>
                                required
                                class="u-radio-compact">
                            <span>
                                <?= e($indirizzoLabel) ?>
                                <?php if (!empty($indirizzo['predefinito'])): ?>
                                    <span class="seller-pro-badge u-style-035">Predefinito</span>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>

                    <button class="btn" type="submit">Continua con PayPal</button>
                </form>
            <?php else: ?>
                <div class="alert alert-error">Aggiungi un indirizzo di spedizione prima di procedere al pagamento.</div>
                <a class="btn" href="index.php?route=profilo">Vai al profilo</a>
            <?php endif; ?>
        </aside>
    </section>
<?php else: ?>
    <div class="alert alert-error">Pagamento non disponibile.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
