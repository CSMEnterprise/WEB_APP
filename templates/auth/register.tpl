{* Pagina scelta tipo account: l'utente sceglie tra registrazione come utente normale o come business prima di procedere al form specifico. *}
{include file="layouts/header.tpl"}

<div class="u-style-092">
    <h1 class="u-style-093">Scegli il tipo di account</h1>
    <p class="muted u-style-094">Entra in NerdVault con il profilo piu adatto alle tue esigenze.</p>
</div>

<div class="grid-2 u-style-095">
    <!-- Utente -->
    <article class="card u-style-096">
        <div>
            <div class="u-style-097">ACCOUNT PERSONALE</div>
            <h2 class="u-style-098">Utente Normale</h2>
            <p class="u-style-099">Ideale per acquistare prodotti, salvare articoli nella wishlist e pubblicare annunci come privato.</p>
            <ul class="u-style-100">
                <li class="u-style-101"><span class="u-style-102">&#10004;</span> Pubblica e gestisci i tuoi annunci</li>
                <li class="u-style-101"><span class="u-style-102">&#10004;</span> Aggiungi prodotti al carrello e wishlist</li>
                <li class="u-style-101"><span class="u-style-102">&#10004;</span> Vota e ricevi feedback</li>
            </ul>
        </div>
        <a class="btn u-style-103" href="/auth/register-user">Registrati come utente</a>
    </article>

    <!-- Business -->
    <article class="card u-style-104">
        <div class="u-style-105">CONSIGLIATO PER NEGOZI</div>
        <div>
            <div class="u-style-106">ACCOUNT PROFESSIONALE</div>
            <h2 class="u-style-098">Business</h2>
            <p class="u-style-099">Pensato per negozi, rivenditori e attivita che vogliono vendere e gestire la propria presenza professionale sul marketplace.</p>
            <ul class="u-style-100">
                <li class="u-style-101"><span class="u-style-107">&#10004;</span> Profilo aziendale e Partita IVA</li>
                <li class="u-style-101"><span class="u-style-107">&#10004;</span> Gestione ordini ricevuti</li>
                <li class="u-style-101"><span class="u-style-107">&#10004;</span> Vendita senza carrello e wishlist</li>
            </ul>
        </div>
        <a class="btn btn-gold u-style-103" href="/auth/register-business">Registrati come business</a>
    </article>
</div>

<div class="u-style-108">
    <p>Hai gia un account? <a class="u-style-109" href="/auth/login">Accedi qui</a></p>
</div>

{include file="layouts/footer.tpl"}
