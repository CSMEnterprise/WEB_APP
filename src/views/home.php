<?php
$pageTitle = 'Home';
require __DIR__ . '/layout/header.php';
?>

<section class="card">
    <h1>Compra e vendi articoli nerd in modo semplice.</h1>
    <p>
        NerdVault è un marketplace per videogiochi, fumetti, action figure, carte collezionabili,
        gadget e prodotti da collezione.
    </p>

    <p>
        <a class="btn" href="index.php?route=annunci">Sfoglia annunci</a>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="btn btn-secondary" href="index.php?route=register">Crea account</a>
        <?php endif; ?>
    </p>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>