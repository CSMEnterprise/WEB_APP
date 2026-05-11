<?php
$pageTitle = 'Dettaglio annuncio';
$annuncio = $annuncio ?? null;
$immagini = $immagini ?? [];
$feedback = $feedback ?? [];
require __DIR__ . '/../layout/header.php';

if (!$annuncio):
?>
    <div class="empty-state">
        <h1>Annuncio non trovato</h1>
        <p>L’annuncio richiesto non esiste o non è più disponibile.</p>
        <a class="btn" href="index.php?action=annunci">Torna agli annunci</a>
    </div>
<?php
require __DIR__ . '/../layout/footer.php';
return;
endif;

$id = $annuncio['id_annuncio'] ?? $annuncio->id_annuncio ?? '';
$titolo = $annuncio['titolo'] ?? $annuncio->titolo ?? 'Annuncio';
$descrizione = $annuncio['descrizione'] ?? $annuncio->descrizione ?? '';
$prezzo = $annuncio['prezzo'] ?? $annuncio->prezzo ?? 0;
$statoConservazione = $annuncio['stato_conservazione'] ?? $annuncio->stato_conservazione ?? '';
$consegna = $annuncio['modalita_consegna'] ?? $annuncio->modalita_consegna ?? '';
$stato = $annuncio['stato'] ?? $annuncio->stato ?? '';
?>

<section class="detail-layout">
    <div class="gallery card">
        <?php if (!empty($immagini)): ?>
            <?php foreach ($immagini as $img): ?>
                <img src="<?= e($img['url'] ?? $img->url ?? '') ?>" alt="<?= e($titolo) ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <div class="placeholder-large">NerdVault</div>
        <?php endif; ?>
    </div>

    <div class="card detail-info">
        <p class="badge"><?= e($stato) ?></p>
        <h1><?= e($titolo) ?></h1>
        <p class="price price-large">€ <?= number_format((float)$prezzo, 2, ',', '.') ?></p>

        <dl class="info-list">
            <dt>Stato conservazione</dt>
            <dd><?= e($statoConservazione) ?></dd>

            <dt>Modalità consegna</dt>
            <dd><?= e(str_replace('_', ' ', $consegna)) ?></dd>
        </dl>

        <p><?= nl2br(e($descrizione)) ?></p>

        <div class="actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="btn" href="index.php?action=carrello-add&id=<?= e($id) ?>">Aggiungi al carrello</a>
                <a class="btn btn-secondary" href="index.php?action=preferito_add&id=<?= e($id) ?>">Preferito</a>
                <a class="link-danger" href="index.php?action=segnalazione_create&id_annuncio=<?= e($id) ?>">Segnala</a>
            <?php else: ?>
                <a class="btn" href="index.php?action=login">Accedi per acquistare</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
