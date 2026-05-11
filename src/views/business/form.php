<?php
$pageTitle = 'Account business';
$business = $business ?? [];
$action = $action ?? 'index.php?action=business_store';
require __DIR__ . '/../layout/header.php';
?>

<section class="card">
    <h1>Account business</h1>
    <p class="muted">Compila i dati dell’azienda o dello store.</p>

    <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form">
        <div class="grid-2">
            <label>
                Partita IVA
                <input type="text" name="p_iva" required maxlength="20"
                       value="<?= e($business['p_iva'] ?? $business->p_iva ?? '') ?>">
            </label>

            <label>
                Nome azienda
                <input type="text" name="nome_azienda" required maxlength="255"
                       value="<?= e($business['nome_azienda'] ?? $business->nome_azienda ?? '') ?>">
            </label>
        </div>

        <label>
            Email aziendale
            <input type="email" name="email_aziendale" required
                   value="<?= e($business['email_aziendale'] ?? $business->email_aziendale ?? '') ?>">
        </label>

        <label>
            Descrizione
            <textarea name="descrizione" rows="5"><?= e($business['descrizione'] ?? $business->descrizione ?? '') ?></textarea>
        </label>

        <div class="grid-2">
            <label>
                Telefono
                <input type="text" name="telefono" maxlength="20"
                       value="<?= e($business['telefono'] ?? $business->telefono ?? '') ?>">
            </label>

            <label>
                Link social
                <input type="url" name="link_social"
                       value="<?= e($business['link_social'] ?? $business->link_social ?? '') ?>">
            </label>
        </div>

        <label>
            Indirizzo
            <textarea name="indirizzo" rows="3"><?= e($business['indirizzo'] ?? $business->indirizzo ?? '') ?></textarea>
        </label>

        <label>
            Logo
            <input type="file" name="logo" accept="image/*">
        </label>

        <button class="btn" type="submit">Salva account business</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
