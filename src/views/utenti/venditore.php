<?php
$pageTitle = 'Profilo venditore';
require __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($venditore)): ?>
    <?php
        $displayName = trim((string)($venditore['username'] ?? 'Venditore'));
        $avatarInitial = strtoupper(substr($displayName !== '' ? $displayName : 'V', 0, 1));
        $annunciCount = is_countable($annunciVenditore ?? null) ? count($annunciVenditore) : 0;
        $feedbackCount = is_countable($feedbackVenditore ?? null) ? count($feedbackVenditore) : 0;
        $media = (float)($mediaVenditore ?? 0);
        $stelle = (int) round($media);
    ?>

    <div class="profile-page">
        <section class="profile-hero" aria-label="Riepilogo venditore">
            <div class="profile-avatar-form">
                <div class="profile-avatar-button" aria-hidden="true">
                    <?php if (!empty($venditore['propic'])): ?>
                        <img src="<?= e($venditore['propic']) ?>" alt="Foto profilo">
                    <?php else: ?>
                        <span class="profile-avatar-initial"><?= e($avatarInitial) ?></span>
                    <?php endif; ?>
                </div>
                <p class="profile-avatar-hint">Profilo pubblico</p>
            </div>

            <div class="profile-summary">
                <span class="profile-kicker">Account venditore</span>
                <h1><?= e($displayName) ?></h1>
                <p class="profile-summary-copy">
                    Visualizza il profilo del venditore e gli annunci attualmente in vendita.
                </p>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <span>Username</span>
                        <strong><?= e($venditore['username'] ?? 'Non indicato') ?></strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Registrato dal</span>
                        <strong><?= !empty($venditore['data_registrazione']) ? e(date('d/m/Y', strtotime((string)$venditore['data_registrazione']))) : 'Non indicato' ?></strong>
                    </div>
                </div>
            </div>

            <div class="profile-stats" aria-label="Statistiche venditore">
                <div class="profile-stat">
                    <span class="profile-stat-value"><?= e((string)$annunciCount) ?></span>
                    <span class="profile-stat-label">Annunci attivi</span>
                </div>
            </div>
        </section>

        <div class="profile-actions">
            <a class="btn btn-secondary" href="index.php?route=feedback-venditore&id=<?= e($venditore['id_utente'] ?? '') ?>">Vedi feedback</a>
        </div>

        <section class="profile-annunci">
            <div class="profile-section-header">
                <div>
                    <h2>Annunci in vendita</h2>
                    <p>Oggetti pubblicati e disponibili da questo venditore.</p>
                </div>
            </div>

            <?php
                $isOwnProfile = !empty($_SESSION['user_id']) && empty($_SESSION['is_admin'])
                    && (int)($_SESSION['user_id'] ?? 0) === (int)($venditore['id_utente'] ?? 0);
            ?>

            <?php if (!empty($annunciVenditore)): ?>
                <div class="grid profile-grid">
                    <?php foreach ($annunciVenditore as $annuncio): ?>
                        <article
                            class="card clickable-card"
                            data-href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                            role="link"
                            tabindex="0">
                            <?php if (!empty($annuncio['immagine_principale'])): ?>
                                <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                            <?php endif; ?>

                            <h3><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h3>
                            <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                            <p class="price">&euro; <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                            <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?: 'Non specificato') ?></p>
                            <p><strong>Stato vendita:</strong> <?= e(ucfirst($annuncio['stato'] ?? '')) ?></p>

                            <div class="profile-card-actions">
                                <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                                <?php if ($isOwnProfile && ($annuncio['stato'] ?? '') === 'attivo'): ?>
                                    <a class="btn btn-secondary" href="index.php?route=annuncio-edit&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Modifica</a>
                                    <a class="btn btn-danger" href="index.php?route=annuncio-delete&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                                       onclick="return confirm('Eliminare questo annuncio?')">Elimina</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card profile-empty">
                    <p>Questo venditore non ha annunci attivi.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php else: ?>
    <div class="alert alert-error">Venditore non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
