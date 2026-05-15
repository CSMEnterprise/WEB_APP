<?php
$pageTitle = 'Risultati Ricerca - NerdVault';

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/footer.php';
?>

<main class="container">
    <h1>
        Risultati per: 
        <span class="muted"><?= e($_SESSION['search_keyword'] ?? '') ?></span>
    </h1>

    <?php if (empty($_SESSION['search_results'])): ?>
        <div class="alert alert-error">
            Nessun annuncio trovato. Prova con parole chiave diverse.
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($_SESSION['search_results'] as $annuncio): ?>
                <div
                    class="card clickable-card"
                    data-href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio'] ?? '') ?>"
                    role="link"
                    tabindex="0">
                    <h3><?= e($annuncio['titolo']) ?></h3>
                    <p class="muted"><?= e($annuncio['descrizione'] ?? '') ?></p>
                    <span class="price"><?= number_format($annuncio['prezzo'], 2) ?>€</span>
                    <small>Categoria: <?= e($annuncio['categoria_nome']) ?></small><br>
                    <small>
                        Venditore:
                        <a href="index.php?route=venditore&id=<?= e($annuncio['id_utente'] ?? '') ?>">
                            <?= e($annuncio['venditore_username']) ?>
                        </a>
                    </small>
                    
                    <?php if (isset($annuncio['id_annuncio'])): ?>
                        <a href="index.php?route=annuncio&id=<?= e($annuncio['id_annuncio']) ?>" class="btn btn-secondary">Dettaglio</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 20px;">
            <a href="index.php?route=annunci" class="btn btn-danger">Torna a tutti gli annunci</a>
        </div>
    <?php endif; ?>

    <!-- Sessione pulita dopo aver mostrato i risultati -->
    <?php if (isset($_SESSION['search_results'])): unset($_SESSION['search_results'], $_SESSION['search_keyword']); endif; ?>
</main>
