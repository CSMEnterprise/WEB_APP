<?php
$pageTitle = 'Scegli registrazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<section class="card register-choice-page">
    <h1>Scegli il tipo di account</h1>
    <p class="muted">
        Registrati su NerdVault scegliendo il profilo più adatto a te.
    </p>

    <div class="register-choice-list">
        <article class="register-choice-card">
            <div>
                <p class="register-choice-kicker">Account personale</p>
                <h2>Utente normale</h2>
                <p>
                    Ideale se vuoi acquistare prodotti, salvare articoli nella wishlist
                    e pubblicare annunci come privato.
                </p>

                <ul class="register-benefits">
                    <li>Pubblica e gestisci i tuoi annunci.</li>
                    <li>Aggiungi prodotti al carrello e alla wishlist.</li>
                    <li>Gestisci il tuo profilo e gli annunci venduti.</li>
                </ul>
            </div>

            <a class="btn" href="index.php?route=register-user">Registrati come utente</a>
        </article>

        <article class="register-choice-card register-choice-card-business">
            <div>
                <p class="register-choice-kicker">Account professionale</p>
                <h2>Business</h2>
                <p>
                    Pensato per negozi, rivenditori e attività che vogliono presentarsi
                    con dati aziendali e gestire la propria presenza sul marketplace.
                </p>

                <ul class="register-benefits">
                    <li>Crea un profilo aziendale collegato al tuo account.</li>
                    <li>Mostra dati business come nome azienda, Partita IVA e sede.</li>
                    <li>Accedi alle funzioni dedicate agli account business.</li>
                </ul>
            </div>

            <a class="btn btn-secondary" href="index.php?route=register-business">Registrati come business</a>
        </article>
    </div>

    <p class="register-choice-login">
        Hai già un account? <a href="index.php?route=login">Accedi</a>
    </p>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
