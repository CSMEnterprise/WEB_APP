<?php
$pageTitle = 'Profilo';
require __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($errore)): ?>
    <div class="alert alert-error"><?= e($errore) ?></div>
<?php endif; ?>

<?php if (!empty($utente)): ?>
    <?php
        $filtroAnnunci = $filtroAnnunci ?? 'attivo';
        $titoloAnnunciProfilo = $titoloAnnunciProfilo ?? 'Annunci attivi';
        $isAttivi = $filtroAnnunci === 'attivo';
        $isVenduti = $filtroAnnunci === 'venduto';
        $isBusiness = !empty($_SESSION['is_business']);
        $displayName = trim((string)($utente['username'] ?? 'Utente'));
        $avatarInitial = strtoupper(substr($displayName !== '' ? $displayName : 'U', 0, 1));
        $hasSpedizione = !empty($utente['via']) || !empty($utente['citta']);
        $viaCompleta = trim(($utente['via'] ?? '') . ' ' . ($utente['numero'] ?? ''));
        $localitaSpedizione = trim(($utente['cap'] ?? '') . ' ' . ($utente['citta'] ?? ''));

        if (!empty($utente['provincia'])) {
            $localitaSpedizione = trim($localitaSpedizione . ' (' . $utente['provincia'] . ')');
        }

        $indirizzoSpedizione = implode(', ', array_filter([$viaCompleta, $localitaSpedizione]));
        $indirizziUtente = $indirizziUtente ?? [];
        $indirizziCount = is_countable($indirizziUtente) ? count($indirizziUtente) : 0;
        $annunciCount = is_countable($annunciUtente ?? null) ? count($annunciUtente) : 0;
        $pagamentiCount = !$isBusiness && is_countable($cronologiaPagamenti ?? null) ? count($cronologiaPagamenti) : 0;
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
                <span class="profile-kicker"><?= $isBusiness ? 'Account business' : 'Account personale' ?></span>
                <h1><?= e($displayName) ?></h1>
                <p class="profile-summary-copy">
                    <?= $isBusiness ? 'Gestisci profilo, annunci e vendite da un unico pannello.' : 'Gestisci profilo, spedizioni, annunci e acquisti da un unico pannello.' ?>
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
                    <?php if (!$isBusiness): ?>
                        <div class="profile-info-item">
                            <span>Indirizzo</span>
                            <strong><?= e($utente['nome'] ?? 'Da completare') ?></strong>
                            <strong><?= $hasSpedizione ? e($indirizzoSpedizione) : 'Da completare' ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="u-style-073">
                    <button type="button" class="btn btn-secondary u-style-074" onclick="toggleForm('editProfiloForm')"
                           >
                        ✏️ Modifica dati
                    </button>
                    <button type="button" class="btn btn-secondary u-style-074" onclick="toggleForm('editPasswordForm')"
                           >
                        🔑 Cambia password
                    </button>
                </div>
            </div>

            <div class="profile-stats" aria-label="Statistiche profilo">
                <div class="profile-stat">
                    <span class="profile-stat-value"><?= e($annunciCount) ?></span>
                    <span class="profile-stat-label"><?= $isVenduti ? 'Annunci venduti' : 'Annunci attivi' ?></span>
                </div>
                <?php if (!$isBusiness): ?>
                    <div class="profile-stat">
                        <span class="profile-stat-value"><?= e($pagamentiCount) ?></span>
                        <span class="profile-stat-label">Acquisti tracciati</span>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php
            $openProfiloEdit  = $openProfiloEdit  ?? (!empty($_GET['profilo_aggiornato']) ? false : false);
            $openPasswordEdit = $openPasswordEdit ?? false;
            $profiloAggiornato   = !empty($_GET['profilo_aggiornato']);
            $passwordAggiornata  = !empty($_GET['password_aggiornata']);
        ?>

        <?php if ($profiloAggiornato): ?>
            <div class="alert alert-success">Profilo aggiornato con successo.</div>
        <?php endif; ?>
        <?php if ($passwordAggiornata): ?>
            <div class="alert alert-success">Password aggiornata con successo.</div>
        <?php endif; ?>

        <div class="profile-actions">
            <?php if (!$isBusiness): ?>
                <button type="button" class="btn" id="toggle-indirizzo-btn" onclick="toggleIndirizzoForm()">
                    <?= $indirizziCount > 0 ? 'Aggiungi un altro indirizzo' : 'Aggiungi indirizzo di spedizione' ?>
                </button>
            <?php endif; ?>
            <a class="btn btn-gold" href="index.php?route=annuncio-create">Crea annuncio</a>
            <a class="btn btn-secondary" href="index.php?route=feedback">I miei feedback</a>
        </div>

        <!-- Form modifica dati profilo -->
        <div id="editProfiloForm" class="card profile-address-form <?= $openProfiloEdit ? '' : 'is-hidden' ?>">
            <h2>Modifica dati personali</h2>
            <form method="post" action="index.php?route=profilo-update">
                <div class="profile-form-grid">
                    <div class="profile-form-wide">
                        <label for="edit_nome">Nome e cognome</label>
                        <input type="text" id="edit_nome" name="nome"
                               value="<?= e($utente['nome'] ?? '') ?>" required>
                    </div>
                    <div class="profile-form-wide">
                        <label for="edit_telefono">Telefono</label>
                        <input type="text" id="edit_telefono" name="telefono"
                               value="<?= e($utente['telefono'] ?? '') ?>"
                               placeholder="+39 333 1234567">
                    </div>
                </div>
                <div class="u-style-075">
                    <button type="submit" class="btn">Salva modifiche</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('editProfiloForm')">Annulla</button>
                </div>
            </form>
        </div>

        <!-- Form cambio password -->
        <div id="editPasswordForm" class="card profile-address-form <?= $openPasswordEdit ? '' : 'is-hidden' ?>">
            <h2>Cambia password</h2>
            <form method="post" action="index.php?route=profilo-password" autocomplete="off">
                <div class="profile-form-grid">
                    <div class="profile-form-wide">
                        <label for="password_attuale">Password attuale</label>
                        <div class="password-wrapper">
                            <input type="password" id="password_attuale" name="password_attuale"
                                   autocomplete="current-password" required>
                            <button class="btn btn-secondary btn-password-toggle" type="button"
                                    onclick="togglePasswordVisibility('password_attuale', this)">Mostra</button>
                        </div>
                    </div>
                    <div>
                        <label for="nuova_password">Nuova password</label>
                        <div class="password-wrapper">
                            <input type="password" id="nuova_password" name="nuova_password"
                                   pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{10,}"
                                   title="Almeno 10 caratteri, una maiuscola e un carattere speciale."
                                   autocomplete="new-password" required>
                            <button class="btn btn-secondary btn-password-toggle" type="button"
                                    onclick="togglePasswordVisibility('nuova_password', this)">Mostra</button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirm">Conferma nuova password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password_confirm" name="password_confirm"
                                   autocomplete="new-password" required>
                            <button class="btn btn-secondary btn-password-toggle" type="button"
                                    onclick="togglePasswordVisibility('password_confirm', this)">Mostra</button>
                        </div>
                    </div>
                </div>
                <p class="muted u-style-076">
                    Almeno 10 caratteri, una lettera maiuscola e un carattere speciale.
                </p>
                <div class="u-style-077">
                    <button type="submit" class="btn">Aggiorna password</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('editPasswordForm')">Annulla</button>
                </div>
            </form>
        </div>

        <?php
            $editingIndirizzo = $editingIndirizzo ?? null;
        ?>

        <?php if (!$isBusiness): ?>
        <!-- Form aggiunta nuovo indirizzo -->
        <div id="indirizzoForm" class="card profile-address-form <?= $editingIndirizzo ? 'is-hidden' : ($indirizziCount > 0 ? 'is-hidden' : '') ?>">
            <h2><?= $indirizziCount > 0 ? 'Nuovo indirizzo di spedizione' : 'Indirizzo di spedizione' ?></h2>

            <form method="post" action="index.php">
                <input type="hidden" name="route" value="profilo-indirizzo-store">

                <div class="profile-form-grid">
                    <div class="profile-form-wide">
                        <label for="nome">Nome e cognome</label>
                        <input type="text" id="nome" name="nome"
                               value="<?= $indirizziCount > 0 ? '' : e($utente['nome'] ?? '') ?>" required>
                    </div>
                    <div class="profile-form-wide">
                        <label for="via">Via / Corso / Piazza</label>
                        <input type="text" id="via" name="via" value="" required>
                    </div>
                    <div>
                        <label for="numero">Numero civico</label>
                        <input type="text" id="numero" name="numero" value="">
                    </div>
                    <div>
                        <label for="cap">CAP</label>
                        <input type="text" id="cap" name="cap" maxlength="5" value="">
                    </div>
                    <div>
                        <label for="citta">Citt&agrave;</label>
                        <input type="text" id="citta" name="citta" value="" required>
                    </div>
                    <div>
                        <label for="provincia">Provincia</label>
                        <input type="text" id="provincia" name="provincia" maxlength="2" value="">
                    </div>
                </div>

                <button type="submit" class="btn">Salva nuovo indirizzo</button>
            </form>
        </div>

        <!-- Form modifica indirizzo esistente -->
        <?php if ($editingIndirizzo): ?>
        <div class="card profile-address-form" id="editIndirizzoForm">
            <h2>Modifica indirizzo</h2>
            <form method="post" action="index.php?route=profilo-indirizzo-update">
                <input type="hidden" name="id_indirizzo" value="<?= e($editingIndirizzo['id_indirizzo']) ?>">
                <div class="profile-form-grid">
                    <div class="profile-form-wide">
                        <label for="edit_via">Via / Corso / Piazza</label>
                        <input type="text" id="edit_via" name="via"
                               value="<?= e($editingIndirizzo['via'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="edit_numero">Numero civico</label>
                        <input type="text" id="edit_numero" name="numero"
                               value="<?= e($editingIndirizzo['numero'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="edit_cap">CAP</label>
                        <input type="text" id="edit_cap" name="cap" maxlength="5"
                               value="<?= e($editingIndirizzo['cap'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="edit_citta">Citt&agrave;</label>
                        <input type="text" id="edit_citta" name="citta"
                               value="<?= e($editingIndirizzo['citta'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="edit_provincia">Provincia</label>
                        <input type="text" id="edit_provincia" name="provincia" maxlength="2"
                               value="<?= e($editingIndirizzo['provincia'] ?? '') ?>">
                    </div>
                </div>
                <div class="u-style-075">
                    <button type="submit" class="btn">Salva modifiche</button>
                    <a href="index.php?route=profilo" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <section>
            <div class="profile-section-header">
                <div>
                    <h2>Tutti gli indirizzi</h2>
                    <p>Visualizza gli indirizzi di spedizione salvati sul tuo profilo.</p>
                </div>
            </div>

            <?php if (!empty($indirizziUtente)): ?>
                <div class="grid">
                    <?php foreach ($indirizziUtente as $indirizzo): ?>
                        <?php
                            $idInd = (int)($indirizzo['id_indirizzo'] ?? 0);
                            $viaIndirizzo = trim(($indirizzo['via'] ?? '') . ' ' . ($indirizzo['numero'] ?? ''));
                            $localitaIndirizzo = trim(($indirizzo['cap'] ?? '') . ' ' . ($indirizzo['citta'] ?? ''));
                            if (!empty($indirizzo['provincia'])) {
                                $localitaIndirizzo = trim($localitaIndirizzo . ' (' . $indirizzo['provincia'] . ')');
                            }
                            $isEditing = $editingIndirizzo && (int)$editingIndirizzo['id_indirizzo'] === $idInd;
                        ?>
                        <article class="card <?= $isEditing ? 'u-card-editing' : '' ?>">
                            <h3 class="u-style-078">
                                Indirizzo <?= !empty($indirizzo['predefinito']) ? '<span class="seller-pro-badge">Predefinito</span>' : '' ?>
                                <?= $isEditing ? '<span class="seller-pro-badge seller-pro-badge-editing">In modifica</span>' : '' ?>
                            </h3>
                            <p><?= e(implode(', ', array_filter([$viaIndirizzo, $localitaIndirizzo]))) ?></p>
                            <p class="muted"><?= e($indirizzo['paese'] ?? 'Italia') ?></p>
                            <div class="u-style-079">
                                <?php if (empty($indirizzo['predefinito'])): ?>
                                    <a class="btn btn-secondary u-address-action"
                                       href="index.php?route=profilo-indirizzo-default&id=<?= $idInd ?>">
                                        Predefinito
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-secondary u-address-action"
                                   href="index.php?route=profilo-indirizzo-edit&id=<?= $idInd ?>">
                                    Modifica
                                </a>
                                <a class="btn btn-danger u-address-action"
                                   href="index.php?route=profilo-indirizzo-delete&id=<?= $idInd ?>"
                                   onclick="return confirm('Eliminare questo indirizzo?')">
                                    Elimina
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card profile-empty">
                    <p>Non hai ancora indirizzi salvati.</p>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

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
                            <p><strong>Conservazione:</strong> <?= e($annuncio['stato_conservazione'] ?: 'Non specificato') ?></p>
                            <p><strong>Stato vendita:</strong> <?= e(ucfirst($annuncio['stato'] ?? '')) ?></p>

                            <div class="profile-card-actions">
                                <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                                <?php if ($isAttivi): ?>
                                    <a class="btn btn-secondary" href="index.php?route=annuncio-edit&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Modifica</a>
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

        <?php if (!$isBusiness): ?>
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
        <?php endif; ?>
    </div>

    <script>
        function toggleForm(formId, triggerEl) {
            const form = document.getElementById(formId);
            if (!form) return;

            const willOpen = form.classList.contains('is-hidden');

            // Chiudi tutti gli altri form collassabili
            ['indirizzoForm', 'editProfiloForm', 'editPasswordForm'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el && id !== formId) el.classList.add('is-hidden');
            });

            form.classList.toggle('is-hidden', !willOpen);

            if (willOpen) {
                setTimeout(function() { form.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
            }
        }

        function toggleIndirizzoForm() {
            toggleForm('indirizzoForm');
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
