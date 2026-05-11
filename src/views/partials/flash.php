<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= e($_SESSION['flash']['type'] ?? 'info') ?>">
        <?= e($_SESSION['flash']['message'] ?? '') ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
