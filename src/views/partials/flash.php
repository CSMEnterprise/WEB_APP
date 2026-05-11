<?php if (!empty($errore)): ?>
    <div class="alert alert-error"><?= e($errore) ?></div>
<?php endif; ?>

<?php if (!empty($successo)): ?>
    <div class="alert alert-success"><?= e($successo) ?></div>
<?php endif; ?>
