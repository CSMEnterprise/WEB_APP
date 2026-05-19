<?php
$pageTitle = 'Home';
require __DIR__ . '/layout/header.php';
?>

<?php
$q = trim($_GET['q'] ?? '');
$idCategoria = (int)($_GET['id_categoria'] ?? 0);
$prezzoMin = $_GET['prezzo_min'] ?? '';
$prezzoMax = $_GET['prezzo_max'] ?? '';
$ordinamento = $_GET['ordinamento'] ?? 'data_desc';
$hasFiltriAvanzati = $prezzoMin !== '' || $prezzoMax !== '' || $ordinamento !== 'data_desc';
$isRicerca = $q !== '' || $idCategoria > 0 || $hasFiltriAvanzati;
$paginaCorrente = max(1, (int)($paginaCorrente ?? ($_GET['page'] ?? 1)));
$totalePagine = max(1, (int)($totalePagine ?? 1));
$totaleAnnunci = (int)($totaleAnnunci ?? count($homeAnnunci ?? []));
$buildPageUrl = static function (int $page): string {
    $params = $_GET;
    $params['route'] = 'home';
    $params['page'] = $page;

    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
        }
    }

    return 'index.php?' . http_build_query($params);
};
?>

<?php if (!$isRicerca && !isset($_SESSION['user_id'])): ?>
<section class="card">
    <h1>Compra e vendi articoli nerd in modo semplice.</h1>
    <p>
        NerdVault è un marketplace per videogiochi, fumetti, action figure, carte collezionabili,
        gadget e prodotti da collezione.
    </p>
    <p>
        <a class="btn btn-secondary" href="index.php?route=register">Crea account</a>
    </p>
</section>
<?php endif; ?>

<?php if ($q !== '' && !empty($utenti)): ?>
<section style="margin-bottom: 32px;">
    <h2>Utenti trovati</h2>
    <div class="grid">
        <?php foreach ($utenti as $u): ?>
            <div class="card clickable-card"
                 data-href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>"
                 role="link" tabindex="0"
                 style="display:flex;align-items:center;gap:14px;">
                <div style="width:54px;height:54px;border-radius:50%;overflow:hidden;background:var(--bg-input);
                            display:flex;align-items:center;justify-content:center;
                            border:1px solid var(--border);flex:0 0 54px;">
                    <?php if (!empty($u['propic'])): ?>
                        <img src="<?= e($u['propic']) ?>" alt="Foto profilo" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <span style="font-size:26px;">&#128100;</span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong><?= e($u['username'] ?? '') ?></strong>
                    <?php if (!empty($u['nome'])): ?>
                        <p class="muted" style="margin:2px 0;"><?= e($u['nome']) ?></p>
                    <?php endif; ?>
                    <a class="btn btn-secondary"
                       style="font-size:12px;padding:5px 10px;margin-top:6px;display:inline-block;"
                       href="index.php?route=venditore&id=<?= e($u['id_utente']) ?>">
                        Vedi profilo
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<style>
    .home-filter-toggle {
        width: 44px;
        height: 44px;
        padding: 0;
        border-radius: 14px;
        flex: 0 0 44px;
    }

    .home-filter-toggle svg {
        width: 21px;
        height: 21px;
        stroke: currentColor;
    }

    .home-filter-panel[hidden] {
        display: none;
    }

    .home-filter-form {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
        gap: 14px;
        align-items: end;
    }

    .home-filter-field input,
    .home-filter-field select {
        max-width: none;
        margin-bottom: 0;
    }

    .home-filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .home-pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 26px 0 4px;
    }

    .home-pagination .btn {
        min-width: 42px;
        padding: 11px 14px;
    }

    .home-pagination-current {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        pointer-events: none;
    }

    .home-pagination-summary {
        width: 100%;
        text-align: center;
        margin: 0 0 4px;
    }

    @media (max-width: 900px) {
        .home-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .home-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 560px) {
        .home-filter-form {
            grid-template-columns: 1fr;
        }

        .home-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<section style="margin-top: 28px;">
    <div class="nav" style="align-items:flex-start;">
        <h2><?= e($homeTitoloAnnunci ?? 'Annunci in evidenza') ?></h2>
        <button
            class="btn btn-secondary home-filter-toggle"
            type="button"
            id="homeFilterToggle"
            aria-label="Filtri"
            aria-controls="homeFilterPanel"
            aria-expanded="<?= $hasFiltriAvanzati ? 'true' : 'false' ?>"
            title="Filtri">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 5h18"></path>
                <path d="M6 12h12"></path>
                <path d="M10 19h4"></path>
            </svg>
        </button>
    </div>

    <div class="card home-filter-panel" id="homeFilterPanel" <?= $hasFiltriAvanzati ? '' : 'hidden' ?>>
        <form class="home-filter-form" method="get" action="index.php">
            <input type="hidden" name="route" value="home">
            <input type="hidden" name="q" value="<?= e($q) ?>">
            <?php if ($idCategoria > 0): ?>
                <input type="hidden" name="id_categoria" value="<?= e((string)$idCategoria) ?>">
            <?php endif; ?>

            <div class="home-filter-field">
                <label for="prezzo_min">Prezzo minimo</label>
                <input
                    type="number"
                    id="prezzo_min"
                    name="prezzo_min"
                    min="0"
                    step="0.01"
                    value="<?= e($prezzoMin) ?>">
            </div>

            <div class="home-filter-field">
                <label for="prezzo_max">Prezzo massimo</label>
                <input
                    type="number"
                    id="prezzo_max"
                    name="prezzo_max"
                    min="0"
                    step="0.01"
                    value="<?= e($prezzoMax) ?>">
            </div>

            <div class="home-filter-field">
                <label for="ordinamento">Ordina per</label>
                <select id="ordinamento" name="ordinamento">
                    <option value="data_desc" <?= $ordinamento === 'data_desc' ? 'selected' : '' ?>>Più recenti</option>
                    <option value="data_asc" <?= $ordinamento === 'data_asc' ? 'selected' : '' ?>>Meno recenti</option>
                    <option value="prezzo_asc" <?= $ordinamento === 'prezzo_asc' ? 'selected' : '' ?>>Prezzo crescente</option>
                    <option value="prezzo_desc" <?= $ordinamento === 'prezzo_desc' ? 'selected' : '' ?>>Prezzo decrescente</option>
                </select>
            </div>

            <div class="home-filter-actions">
                <button class="btn" type="submit">Applica</button>
                <a class="btn btn-secondary" href="index.php?route=home<?= $q !== '' ? '&q=' . urlencode($q) : '' ?><?= $idCategoria > 0 ? '&id_categoria=' . urlencode((string)$idCategoria) : '' ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if (!empty($homeAnnunci)): ?>
        <div class="grid">
            <?php foreach ($homeAnnunci as $annuncio): ?>
                <article
                    class="card clickable-card annuncio-card"
                    data-href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                    role="link"
                    tabindex="0">
                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business']) && (int)($annuncio['id_utente'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)): ?>
                        <?php $isInWishlist = in_array((int)($annuncio['id_annuncio'] ?? 0), $wishlistIds ?? [], true); ?>
                        <a
                            class="wishlist-heart <?= $isInWishlist ? 'wishlist-heart-active' : '' ?>"
                            href="index.php?route=wishlist-toggle&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                            title="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>"
                            aria-label="<?= $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist' ?>">
                            &hearts;
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($annuncio['immagine_principale'])): ?>
                        <img class="annuncio-card-img" src="<?= e($annuncio['immagine_principale']) ?>" alt="Foto annuncio">
                    <?php endif; ?>

                    <h3><?= e($annuncio['titolo'] ?? 'Annuncio') ?></h3>
                    <p class="muted"><?= e($annuncio['categoria_nome'] ?? 'Senza categoria') ?></p>
                    <p><?= e($annuncio['descrizione'] ?? '') ?></p>
                    <p class="price">€ <?= number_format((float)($annuncio['prezzo'] ?? 0), 2, ',', '.') ?></p>
                    <p>
                        <strong>Venditore:</strong>
                        <a href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                            <span class="seller-name-line">
                                <?= e(!empty($annuncio['venditore_business_id']) ? ($annuncio['venditore_nome_azienda'] ?? '') : ($annuncio['venditore_username'] ?? '')) ?>
                                <?php if (!empty($annuncio['venditore_business_id'])): ?>
                                    <span class="seller-pro-badge">PRO</span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </p>

                    <a class="btn" href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Dettagli</a>

                    <?php if (!empty($_SESSION['user_id']) && empty($_SESSION['is_admin']) && empty($_SESSION['is_business'])): ?>
                        <?php if ((int)($annuncio['id_utente'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)): ?>
                            <p class="muted" style="margin:4px 0 0;">È un tuo annuncio.</p>
                        <?php elseif (in_array((int)($annuncio['id_annuncio'] ?? 0), $carrelloIds ?? [], true)): ?>
                            <span class="btn btn-secondary" style="opacity:.55;cursor:default;pointer-events:none;">✓ Nel carrello</span>
                        <?php else: ?>
                            <a class="btn btn-secondary" href="index.php?route=carrello-add&id=<?= e($annuncio['id_annuncio'] ?? '') ?>">Aggiungi al carrello</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalePagine > 1): ?>
            <nav class="home-pagination" aria-label="Paginazione annunci">
                <p class="muted home-pagination-summary">
                    Pagina <?= e((string)$paginaCorrente) ?> di <?= e((string)$totalePagine) ?>
                    · <?= e((string)$totaleAnnunci) ?> annunci
                </p>

                <?php if ($paginaCorrente > 1): ?>
                    <a class="btn btn-secondary" href="<?= e($buildPageUrl($paginaCorrente - 1)) ?>">Precedente</a>
                <?php endif; ?>

                <?php
                $paginaDa = max(1, $paginaCorrente - 2);
                $paginaA = min($totalePagine, $paginaCorrente + 2);
                ?>

                <?php if ($paginaDa > 1): ?>
                    <a class="btn btn-secondary" href="<?= e($buildPageUrl(1)) ?>">1</a>
                    <?php if ($paginaDa > 2): ?>
                        <span class="muted">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($pagina = $paginaDa; $pagina <= $paginaA; $pagina++): ?>
                    <?php if ($pagina === $paginaCorrente): ?>
                        <span class="btn home-pagination-current" aria-current="page"><?= e((string)$pagina) ?></span>
                    <?php else: ?>
                        <a class="btn btn-secondary" href="<?= e($buildPageUrl($pagina)) ?>"><?= e((string)$pagina) ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($paginaA < $totalePagine): ?>
                    <?php if ($paginaA < $totalePagine - 1): ?>
                        <span class="muted">...</span>
                    <?php endif; ?>
                    <a class="btn btn-secondary" href="<?= e($buildPageUrl($totalePagine)) ?>"><?= e((string)$totalePagine) ?></a>
                <?php endif; ?>

                <?php if ($paginaCorrente < $totalePagine): ?>
                    <a class="btn btn-secondary" href="<?= e($buildPageUrl($paginaCorrente + 1)) ?>">Successiva</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="card">
            <p>Nessun annuncio disponibile al momento.</p>
        </div>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('homeFilterToggle');
    const panel = document.getElementById('homeFilterPanel');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isHidden = panel.hasAttribute('hidden');
        panel.toggleAttribute('hidden', !isHidden);
        toggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
    });
});
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>
