<?php
$pageTitle = 'Home';
require __DIR__ . '/layout/header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Marketplace</p>
        <h1>Compra e vendi articoli nerd in modo semplice.</h1>
        <p>
            NerdVault è pensato per videogiochi, fumetti, action figure, carte collezionabili,
            gadget e prodotti da collezione.
        </p>
        <div class="actions">
            <a class="btn" href="index.php?route=annunci">Sfoglia annunci</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a class="btn btn-secondary" href="index.php?route=register">Crea account</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>
