<?php
$pageTitle = 'Profilo';
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
        cursor: pointer;
        background:
            radial-gradient(circle at 34% 22%, rgba(255, 255, 255, .32), transparent 25%),
            linear-gradient(135deg, var(--accent), var(--gold));
        box-shadow: 0 20px 45px rgba(0, 0, 0, .36), 0 0 0 8px rgba(124, 58, 237, .16);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .profile-avatar-button:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 24px 60px rgba(0, 0, 0, .44), 0 0 0 10px rgba(245, 158, 11, .13);
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

    .profile-filter-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 14px;
    }

    .profile-filter-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .profile-filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .profile-filter-actions .btn {
        min-height: 38px;
        padding: 9px 13px;
        border-radius: 10px;
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
        gap: 8px;
        flex-wrap: wrap;
        margin-top: auto;
    }

    .profile-card-actions .btn {
        border-radius: 10px;
    }

    .profile-empty {
        padding: 26px;
        border-style: dashed;
        text-align: center;
    }

    .profile-address-form {
        border-radius: 18px;
    }

    .profile-address-form.is-hidden {
        display: none;
    }

    .profile-address-form h2 {
        margin-bottom: 18px;
        font-size: 30px;
    }

    .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 16px;
        max-width: 760px;
    }

    .profile-form-grid .profile-form-wide {
        grid-column: 1 / -1;
    }

    .profile-table-wrap {
        max-width: 100%;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--bg-card);
    }

    .profile-table-wrap table {
        min-width: 780px;
    }

    .profile-status-pill {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(124, 58, 237, .16);
        color: #ddd6fe;
        font-size: 12px;
        font-weight: 800;
    }

    .profile-status-completato {
        background: rgba(34, 197, 94, .14);
        color: #86efac;
    }

    .profile-table-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .profile-table-actions .btn,
    .profile-feedback-done {
        min-height: 32px;
        padding: 6px 10px;
        border-radius: 9px;
        font-size: 12px;
    }

    .profile-feedback-done {
        display: inline-flex;
        align-items: center;
        color: #86efac;
        font-weight: 800;
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
        .profile-stats,
        .profile-form-grid {
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

        .profile-actions .btn,
        .profile-filter-actions,
        .profile-filter-actions .btn {
            width: 100%;
        }
    }
</style>

<?php if (!empty($errore)): ?>
    <div class="alert alert-error"><?= e($errore) ?></div>
<?php endif; ?>

<?php if (!empty($utente)): ?>
    <?php
        $filtroAnnunci = $filtroAnnunci ?? 'attivo';
        $titoloAnnunciProfilo = $titoloAnnunciProfilo ?? 'Annunci attivi';
        $isAttivi = $filtroAnnunci === 'attivo';
        $isVenduti = $filtroAnnunci === 'venduto';
        $displayName = trim((string)($utente['username'] ?? 'Utente'));
        $avatarInitial = strtoupper(substr($displayName !== '' ? $displayName : 'U', 0, 1));
        $hasSpedizione = !empty($utente['via']) || !empty($utente['citta']);
        $viaCompleta = trim(($utente['via'] ?? '') . ' ' . ($utente['numero'] ?? ''));
        $localitaSpedizione = trim(($utente['cap'] ?? '') . ' ' . ($utente['citta'] ?? ''));

        if (!empty($utente['provincia'])) {
            $localitaSpedizione = trim($localitaSpedizione . ' (' . $utente['provincia'] . ')');
        }

        $indirizzoSpedizione = implode(', ', array_filter([$viaCompleta, $localitaSpedizione]));
        $annunciCount = is_countable($annunciUtente ?? null) ? count($annunciUtente) : 0;
        $pagamentiCount = is_countable($cronologiaPagamenti ?? null) ? count($cronologiaPagamenti) : 0;
    ?>

    <div class="profile-page">
        <section class="profile-hero" aria-label="Riepilogo profilo">
            <form method="post" action="index.php?route=profilo-propic-store"
                  enctype="multipart/form-data" id="propic-form" class="profile-avatar-form">
                <input type="file" id="propic-input" name="propic"
                       accept="image/jpeg,image/png,image/webp" hidden>
                <button type="button" class="profile-avatar-button"
                        onclick="document.getElementById('propic-input').click()"
                        title="Clicca per cambiare foto profilo">
                    <?php if (!empty($utente['propic'])): ?>
                        <img src="<?= e($utente['propic']) ?>" alt="Foto profilo">
                    <?php else: ?>
                        <span class="profile-avatar-initial"><?= e($avatarInitial) ?></span>
                    <?php endif; ?>
                </button>
                <p class="profile-avatar-hint">Clicca per aggiornare</p>
            </form>

            <div class="profile-summary">
                <span class="profile-kicker">Account personale</span>
                <h1><?= e($displayName) ?></h1>
                <p class="profile-summary-copy">
                    Gestisci profilo, spedizioni, annunci e acquisti da un unico pannello.
                </p>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <span>Email</span>
                        <strong><?= e($utente['email'] ?? 'Non indicata') ?></strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Telefono</span>
                        <strong><?= e($utente['telefono'] ?? 'Non indicato') ?></strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Nome spedizione</span>
                        <strong><?= e($utente['nome'] ?? 'Da completare') ?></strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Indirizzo</span>
                        <strong><?= $hasSpedizione ? e($indirizzoSpedizione) : 'Da completare' ?></strong>
                    </div>
                </div>
            </div>

            <div class="profile-stats" aria-label="Statistiche profilo">
                <div class="profile-stat">
                    <span class="profile-stat-value"><?= e($annunciCount) ?></span>
                    <span class="profile-stat-label"><?= $isVenduti ? 'Annunci venduti' : 'Annunci attivi' ?></span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-value"><?= e($pagamentiCount) ?></span>
                    <span class="profile-stat-label">Acquisti tracciati</span>
                </div>
            </div>
        </section>

        <div class="profile-actions">
            <button type="button" class="btn" id="toggle-indirizzo-btn" onclick="toggleIndirizzoForm()">
                <?= $hasSpedizione ? 'Modifica indirizzo di spedizione' : 'Aggiungi indirizzo di spedizione' ?>
            </button>

            <a class="btn btn-gold" href="index.php?route=annuncio-create">Crea annuncio</a>
            <a class="btn btn-secondary" href="index.php?route=feedback">I miei feedback</a>
        </div>

        <div id="indirizzoForm" class="card profile-address-form is-hidden">
            <h2>Indirizzo di spedizione</h2>

            <form method="post" action="index.php">
                <input type="hidden" name="route" value="profilo-indirizzo-store">

                <div class="profile-form-grid">
                    <div class="profile-form-wide">
                        <label for="nome">Nome e cognome</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= e($utente['nome'] ?? '') ?>"
                            required>
                    </div>

                    <div class="profile-form-wide">
                        <label for="via">Via / Corso / Piazza</label>
                        <input
                            type="text"
                            id="via"
                            name="via"
                            value="<?= e($utente['via'] ?? '') ?>"
                            required>
                    </div>

                    <div>
                        <label for="numero">Numero civico</label>
                        <input
                            type="text"
                            id="numero"
                            name="numero"
                            value="<?= e($utente['numero'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="cap">CAP</label>
                        <input
                            type="text"
                            id="cap"
                            name="cap"
                            maxlength="5"
                            value="<?= e($utente['cap'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="citta">Citt&agrave;</label>
                        <input
                            type="text"
                            id="citta"
                            name="citta"
                            value="<?= e($utente['citta'] ?? '') ?>"
                            required>
                    </div>

                    <div>
                        <label for="provincia">Provincia</label>
                        <input
                            type="text"
                            id="provincia"
                            name="provincia"
                            maxlength="2"
                            value="<?= e($utente['provincia'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn">Salva indirizzo</button>
            </form>
        </div>

        <section class="profile-annunci">
            <div class="profile-section-header">
                <div>
                    <h2><?= e($titoloAnnunciProfilo) ?></h2>
                    <p>Controlla gli oggetti pubblicati e passa rapidamente tra attivi e venduti.</p>
                </div>
            </div>

            <div class="card profile-filter-card">
                <span class="profile-filter-label">Mostra annunci</span>
                <div class="profile-filter-actions">
                    <a
                        class="btn <?= $isAttivi ? '' : 'btn-secondary' ?>"
                        href="index.php?route=profilo-annunci-attivi">
                        Annunci attivi
                    </a>
                    <a
                        class="btn <?= $isVenduti ? '' : 'btn-secondary' ?>"
                        href="index.php?route=profilo-annunci-venduti">
                        Annunci venduti
                    </a>
                </div>
            </div>

            <?php if (!empty($annunciUtente)): ?>
                <div class="grid profile-grid">
                    <?php foreach ($annunciUtente as $annuncio): ?>
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
                            <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?? '') ?></p>
                            <p><strong>Stato vendita:</strong> <?= e($annuncio['stato'] ?? '') ?></p>

                            <div class="profile-card-actions">
                                <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                                <?php if ($isAttivi): ?>
                                    <a class="btn btn-danger" href="index.php?route=annuncio-delete&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Elimina</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card profile-empty">
                    <?php if ($isVenduti): ?>
                        <p>Non hai ancora annunci venduti.</p>
                    <?php else: ?>
                        <p>Non hai annunci attivi.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <div class="profile-section-header">
                <div>
                    <h2>Cronologia pagamenti</h2>
                    <p>Rivedi gli acquisti conclusi e lascia feedback quando disponibile.</p>
                </div>
            </div>

            <?php if (!empty($cronologiaPagamenti)): ?>
                <div class="profile-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Annuncio</th>
                                <th>Venditore</th>
                                <th>Importo</th>
                                <th>Stato</th>
                                <th>Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cronologiaPagamenti as $p): ?>
                                <?php
                                    $statoPagamento = (string)($p['stato'] ?? '');
                                    $statoClass = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $statoPagamento));
                                ?>
                                <tr>
                                    <td><?= e($p['id_pagamento']) ?></td>
                                    <td><?= e($p['annuncio_titolo'] ?? '-') ?></td>
                                    <td><?= e($p['venditore_username'] ?? '-') ?></td>
                                    <td>&euro; <?= number_format((float)($p['importo_totale'] ?? 0), 2, ',', '.') ?></td>
                                    <td>
                                        <span class="profile-status-pill profile-status-<?= e($statoClass) ?>">
                                            <?= e($statoPagamento) ?>
                                        </span>
                                    </td>
                                    <td><?= e($p['data'] ?? '') ?></td>
                                    <td>
                                        <div class="profile-table-actions">
                                            <a class="btn btn-secondary"
                                               href="index.php?route=annuncio&id=<?= e($p['annuncio_id']) ?>">
                                                Vedi annuncio
                                            </a>
                                            <?php if ($statoPagamento === 'Completato'): ?>
                                                <?php if (!empty($p['feedback_id'])): ?>
                                                    <span class="profile-feedback-done">Feedback inviato</span>
                                                <?php else: ?>
                                                    <a class="btn"
                                                       href="index.php?route=feedback-create&id_pagamento=<?= e($p['id_pagamento']) ?>">
                                                        Lascia feedback
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card profile-empty">
                    <p>Nessun acquisto effettuato.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        function toggleIndirizzoForm() {
            const form = document.getElementById('indirizzoForm');
            const trigger = document.getElementById('toggle-indirizzo-btn');

            if (!form) {
                return;
            }

            form.classList.toggle('is-hidden');

            if (trigger) {
                trigger.setAttribute('aria-expanded', String(!form.classList.contains('is-hidden')));
            }
        }

        const propicInput = document.getElementById('propic-input');

        if (propicInput) {
            propicInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    document.getElementById('propic-form').submit();
                }
            });
        }
    </script>
<?php else: ?>
    <div class="alert alert-error">Utente non trovato.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
