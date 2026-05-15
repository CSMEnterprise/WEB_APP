<?php
$pageTitle = 'Registrazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<h1>Scegli il tipo di account</h1>

<div class="grid-2">
    <section class="card">
        <h2>Utente normale</h2>
        <p>
            Account personale per acquistare prodotti, usare il carrello,
            salvare annunci e pubblicare annunci da privato.
        </p>

        <ul>
            <li>Username personale</li>
            <li>Email di accesso</li>
            <li>Telefono associato al profilo</li>
            <li>Possibilità di aggiungere un indirizzo di spedizione dal profilo</li>
        </ul>

        <a class="btn" href="index.php?route=register-user">
            Registrati come utente
        </a>
    </section>

    <section class="card">
        <h2>Utente business</h2>
        <p>
            Account dedicato ad aziende, negozi e venditori professionali,
            con dati aziendali e referente.
        </p>

        <ul>
            <li>Nome azienda e Partita IVA</li>
            <li>Email aziendale usata anche per il login</li>
            <li>Telefono e referente aziendale</li>
            <li>Sede aziendale salvata nella tabella indirizzi</li>
        </ul>

        <a class="btn" href="index.php?route=register-business">
            Registrati come business
        </a>
    </section>
</div>

<p>
    Hai già un account?
    <a href="index.php?route=login">Accedi</a>
</p>

<?php require __DIR__ . '/../layout/footer.php'; ?>
