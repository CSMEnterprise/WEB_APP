<?php
$pageTitle = 'Lascia un feedback';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="card">
    <h1>Lascia un feedback</h1>

    <?php if (!empty($pagamento)): ?>
        <p>Stai valutando l'acquisto di <strong><?= e($pagamento['titolo'] ?? '') ?></strong>.</p>
    <?php endif; ?>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-error"><?= e($errore) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?route=feedback-store">
        <input type="hidden" name="id_pagamento" value="<?= e($idPagamento ?? '') ?>">

        <label>Valutazione</label>
        <div class="u-style-014">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label class="u-style-015">
                    <input class="u-style-013" type="radio" name="valutazione" value="<?= $i ?>"
                        <?= (int)($_POST['valutazione'] ?? 0) === $i ? 'checked' : '' ?>
                        required>
                    <span class="star" data-val="<?= $i ?>">★</span>
                </label>
            <?php endfor; ?>
        </div>

        <label for="commento">Commento</label>
        <textarea id="commento" name="commento" placeholder="Descrivi la tua esperienza..."><?= e($_POST['commento'] ?? '') ?></textarea>

        <button class="btn" type="submit">Invia feedback</button>
        <a class="btn btn-secondary" href="index.php?route=profilo">Annulla</a>
    </form>
</div>

<script>
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.style.color = '#d1d5db';
        star.addEventListener('mouseenter', () => {
            const val = +star.dataset.val;
            stars.forEach(s => s.style.color = +s.dataset.val <= val ? '#f59e0b' : '#d1d5db');
        });
        star.addEventListener('mouseleave', updateStars);
        star.addEventListener('click', () => {
            star.previousElementSibling.checked = true;
            updateStars();
        });
    });

    function updateStars() {
        const checked = document.querySelector('input[name="valutazione"]:checked');
        const val = checked ? +checked.value : 0;
        stars.forEach(s => s.style.color = +s.dataset.val <= val ? '#f59e0b' : '#d1d5db');
    }

    updateStars();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
