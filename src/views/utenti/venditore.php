<?php
$pageTitle = 'Profilo venditore';
require __DIR__ . '/../layout/header.php';
?>

<style>
    .profile-page {
        display: grid;
        gap: 22px;
        min-width: 0;
    }

    .profile-page > * {
        min-width: 0;
    }

    .profile-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) minmax(230px, .32fr);
        gap: 26px;
        align-items: stretch;
        padding: clamp(22px, 4vw, 34px);
        border: 1px solid rgba(167, 139, 250, .34);
        border-radius: 24px;
        background:
            linear-gradient(135deg, rgba(124, 58, 237, .22), rgba(17, 17, 31, .92) 44%, rgba(245, 158, 11, .12)),
            var(--bg-card);
        box-shadow: 0 28px 80px rgba(0, 0, 0, .36), inset 0 1px 0 rgba(255, 255, 255, .05);
    }

    .profile-hero::before {
        content: "";
        position: absolute;
        inset: -90px -100px auto auto;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, .25), transparent 68%);
        pointer-events: none;
    }

    .profile-hero::after {
        content: "NERDVAULT";
        position: absolute;
        right: 20px;
        bottom: -16px;
        color: rgba(255, 255, 255, .035);
        font-size: clamp(60px, 12vw, 150px);
        font-weight: 800;
        line-height: .8;
        letter-spacing: -.08em;
        pointer-events: none;
    }

    .profile-avatar-form {
        position: relative;
        z-index: 1;
        display: grid;
        align-content: center;
        justify-items: center;
        gap: 10px;
    }

    .profile-avatar-button {
        position: relative;
        width: clamp(118px, 14vw, 154px);
        aspect-ratio: 1;
        padding: 0;
        border: 3px solid rgba(255, 255, 255, .88);
        border-radius: 50%;
        overflow: hidden;
        background:
            radial-gradient(circle at 34% 22%, rgba(255, 255, 255, .32), transparent 25%),
            linear-gradient(135deg, var(--accent), var(--gold));
        box-shadow: 0 20px 45px rgba(0, 0, 0, .36), 0 0 0 8px rgba(124, 58, 237, .16);
    }

    .profile-avatar-button img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .profile-avatar-initial {
        display: grid;
        place-items: center;
        width: 100%;
        height: 100%;
        color: #fff;
        font-size: clamp(48px, 8vw, 72px);
        font-weight: 800;
        text-transform: uppercase;
    }

    .profile-avatar-hint {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        text-align: center;
    }

    .profile-summary,
    .profile-stats {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .profile-kicker {
        display: inline-flex;
        width: fit-content;
        padding: 6px 10px;
        border: 1px solid rgba(245, 158, 11, .34);
        border-radius: 999px;
        background: rgba(245, 158, 11, .10);
        color: #fbbf24;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .profile-summary h1 {
        margin: 12px 0 10px;
        font-size: clamp(38px, 6vw, 78px);
        line-height: .92;
        letter-spacing: -.06em;
    }

    .profile-summary-copy {
        max-width: 650px;
        margin: 0 0 18px;
        color: #c7c7dc;
        font-size: 15px;
    }

    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .profile-info-item {
        min-width: 0;
        padding: 13px 14px;
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 14px;
        background: rgba(255, 255, 255, .045);
    }

    .profile-info-item span {
        display: block;
        margin-bottom: 4px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .profile-info-item strong {
        display: block;
        overflow-wrap: anywhere;
        color: var(--text);
        font-size: 14px;
    }

    .profile-rating {
        color: #f59e0b;
        white-space: nowrap;
    }

    .profile-stats {
        display: grid;
        align-content: center;
        gap: 12px;
        min-width: 0;
    }

    .profile-stat {
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, .10);
        border-radius: 16px;
        background: rgba(255, 255, 255, .055);
    }

    .profile-stat-value {
        display: block;
        color: #fff;
        font-size: 34px;
        font-weight: 800;
        line-height: 1;
    }

    .profile-stat-label {
        display: block;
        margin-top: 5px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .profile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin: 2px 0 4px;
    }

    .profile-actions .btn {
        min-height: 44px;
        border-radius: 12px;
    }

    .profile-section-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin: 8px 0 14px;
    }

    .profile-section-header h2 {
        margin: 0;
        font-size: clamp(28px, 4vw, 44px);
        letter-spacing: -.05em;
    }

    .profile-section-header p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 14px;
    }

    .profile-grid .card {
        border-radius: 18px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .035), transparent 70%),
            var(--bg-card);
    }

    .profile-grid .annuncio-card-img {
        height: 210px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .profile-card-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-top: auto;
        padding-top: 16px;
    }

    .profile-card-actions .btn {
        width: 100%;
        max-width: 200px;
        border-radius: 14px;
    }

    .profile-empty {
        padding: 26px;
        border-style: dashed;
        text-align: center;
    }

    @media (max-width: 980px) {
        .profile-hero {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .profile-stats {
            grid-column: 1 / -1;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 680px) {
        .profile-hero,
        .profile-info-grid,
        .profile-stats {
            grid-template-columns: 1fr;
        }

        .profile-hero {
            text-align: center;
        }

        .profile-kicker,
        .profile-actions {
            margin-left: auto;
            margin-right: auto;
            justify-content: center;
        }

        .profile-section-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-actions .btn {
            width: 100%;
        }
    }
</style>

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
