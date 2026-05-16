<?php
$pageTitle = 'Scegli registrazione';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div style="text-align: center; margin-bottom: 40px; margin-top: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 12px;">Scegli il tipo di account</h1>
    <p class="muted" style="font-size: 16px;">Entra in NerdVault con il profilo piu adatto alle tue esigenze.</p>
</div>

<div class="grid-2" style="max-width: 900px; margin: 0 auto;">
    <!-- Utente -->
    <article class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: inline-block; padding: 6px 12px; background: rgba(124,58,237,.1); color: var(--accent); border-radius: 20px; font-weight: 600; font-size: 12px; margin-bottom: 16px;">ACCOUNT PERSONALE</div>
            <h2 style="margin-bottom: 16px;">Utente Normale</h2>
            <p style="color: var(--muted); margin-bottom: 24px; line-height: 1.5;">Ideale per acquistare prodotti, salvare articoli nella wishlist e pubblicare annunci come privato.</p>
            <ul style="list-style: none; padding: 0; margin: 0 0 32px 0;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--accent);">&#10004;</span> Pubblica e gestisci i tuoi annunci</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--accent);">&#10004;</span> Aggiungi prodotti al carrello e wishlist</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--accent);">&#10004;</span> Vota e ricevi feedback</li>
            </ul>
        </div>
        <a class="btn" href="index.php?route=register-user" style="width: 100%;">Registrati come utente</a>
    </article>

    <!-- Business -->
    <article class="card" style="display: flex; flex-direction: column; justify-content: space-between; border: 2px solid var(--gold); position: relative;">
        <!-- Badge -->
        <div style="position: absolute; top: -12px; right: 20px; background: var(--gold); color: #000; padding: 4px 12px; border-radius: 12px; font-weight: 800; font-size: 12px; box-shadow: 0 4px 12px rgba(250,204,21,.3);">CONSIGLIATO PER NEGOZI</div>

        <div>
            <div style="display: inline-block; padding: 6px 12px; background: rgba(250,204,21,.1); color: var(--gold); border-radius: 20px; font-weight: 600; font-size: 12px; margin-bottom: 16px;">ACCOUNT PROFESSIONALE</div>
            <h2 style="margin-bottom: 16px;">Business</h2>
            <p style="color: var(--muted); margin-bottom: 24px; line-height: 1.5;">Pensato per negozi, rivenditori e attivita che vogliono vendere e gestire la propria presenza professionale sul marketplace.</p>
            <ul style="list-style: none; padding: 0; margin: 0 0 32px 0;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">&#10004;</span> Profilo aziendale e Partita IVA</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">&#10004;</span> Gestione ordini ricevuti</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">&#10004;</span> Vendita senza carrello e wishlist</li>
            </ul>
        </div>
        <a class="btn btn-gold" href="index.php?route=register-business" style="width: 100%;">Registrati come business</a>
    </article>
</div>

<div style="text-align: center; margin-top: 32px; padding-bottom: 40px;">
    <p>Hai gia un account? <a href="index.php?route=login" style="color: var(--accent); font-weight: 600;">Accedi qui</a></p>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
