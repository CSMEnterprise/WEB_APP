{* Pagina scelta tipo account: l'utente sceglie tra registrazione come utente normale o come business prima di procedere al form specifico. *}
{include file="layouts/header.tpl"}

<div class="au-layout">
    <section class="au-form-side">
        <div class="au-tabs" role="tablist" aria-label="Registrazione">
            <a class="au-tab" href="/auth/login">Accedi</a>
            <a class="au-tab is-active" href="/auth/register">Registrati</a>
            <span class="au-tab-bg au-tab-bg-right"></span>
        </div>

        <div class="au-panel">
            <h1 class="au-title">Crea il tuo account.</h1>
            <p class="au-sub">Scegli il profilo piu adatto: collezionista oppure venditore PRO.</p>

            <div class="au-type-grid">
                <a class="au-type-card" href="/auth/register-user">
                    <span class="au-type-glyph" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <strong>Account collezionista</strong>
                    <span class="au-type-desc">Per comprare, vendere occasionalmente e salvare wishlist.</span>
                    <ul class="au-type-list">
                        <li>Wishlist e carrello</li>
                        <li>Pubblica e gestisci annunci</li>
                        <li>Feedback verificati</li>
                    </ul>
                    <span class="au-type-radio"><span></span></span>
                </a>

                <a class="au-type-card au-type-card-pro" href="/auth/register-business">
                    <span class="au-type-flag">Consigliato per venditori</span>
                    <span class="au-type-glyph au-type-glyph-pro" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v4"></path><path d="M12 17v4"></path><path d="M3 12h4"></path><path d="M17 12h4"></path><path d="m5.6 5.6 2.8 2.8"></path><path d="m15.6 15.6 2.8 2.8"></path><path d="m18.4 5.6-2.8 2.8"></path><path d="m8.4 15.6-2.8 2.8"></path></svg>
                    </span>
                    <strong>Account <span>PRO</span></strong>
                    <span class="au-type-desc">Negozio dedicato, ordini e badge professionale.</span>
                    <ul class="au-type-list">
                        <li>Profilo aziendale</li>
                        <li>Gestione ordini ricevuti</li>
                        <li>Badge PRO nelle ricerche</li>
                    </ul>
                    <span class="au-type-radio"><span></span></span>
                </a>
            </div>

            <p class="au-foot">Hai gia un account? <a href="/auth/login">Accedi</a></p>
        </div>
    </section>

    <aside class="au-aside">
        <div class="au-aside-inner">
            <div class="au-aside-badge">NerdVault PRO</div>
            <h2 class="au-aside-title">Una vetrina<br>per ogni tipo<br>di <span>collezione</span>.</h2>
            <p class="au-aside-desc">Scegli come entrare: collezionista per acquistare e vendere ogni tanto, PRO per aprire una vera vetrina.</p>
            <div class="au-aside-cards">
                <div class="au-aside-card">
                    <div class="au-product-art au-product-art-figure"><span>FIG</span></div>
                    <div class="au-aside-card-body"><strong>Figure premium</strong><span>Account personale</span></div>
                </div>
                <div class="au-aside-card au-aside-card-shift">
                    <div class="au-product-art au-product-art-pro"><span>PRO</span></div>
                    <div class="au-aside-card-body"><strong>Vetrina venditore</strong><span>Badge PRO</span></div>
                </div>
            </div>
            <div class="au-aside-quote">
                <span class="au-quote-mark">"</span>
                <p>Account personale per iniziare, profilo PRO quando il vault diventa un negozio vero.</p>
                <div class="au-quote-by"><span class="au-quote-av">NV</span><span>Scelta guidata account</span></div>
            </div>
        </div>
    </aside>
</div>

{include file="layouts/footer.tpl"}
