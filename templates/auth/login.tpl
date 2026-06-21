{* Pagina login: form email/password con link a recupero password. Mostra un alert specifico se l'email non e ancora verificata, con link al reinvio. *}
{include file="layouts/header.tpl"}

<div class="au-layout">
    <section class="au-form-side">
        <div class="au-tabs" role="tablist" aria-label="Accesso">
            <a class="au-tab is-active" href="/auth/login">Accedi</a>
            <a class="au-tab" href="/auth/register">Registrati</a>
            <span class="au-tab-bg"></span>
        </div>

        <div class="au-panel">
            <h1 class="au-title">Bentornato.</h1>
            <p class="au-sub">Accedi per gestire annunci, ordini e wishlist.</p>

            {if !empty($resetOk)}
                <div class="pg-alert" data-tone="success">Password aggiornata! Ora puoi accedere.</div>
            {/if}

            {if !empty($errore)}
                {if !empty($isEmailNonVerificata)}
                    <div class="pg-alert" data-tone="danger">
                        <span><strong>Email non verificata.</strong><br>
                        Controlla la tua casella oppure
                        <a href="/auth/verifica-email-attesa?email={$emailNonVerificataUrl nofilter}">richiedi un nuovo link</a>.</span>
                    </div>
                {else}
                    <div class="pg-alert" data-tone="danger">{$errore}</div>
                {/if}
            {/if}

            <form class="au-form" method="post" action="/auth/login">
                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                <div class="pg-field">
                    <label class="pg-label" for="email">Email</label>
                    <input class="pg-input" type="email" id="email" name="email" placeholder="tu@example.com" required>
                </div>

                <div class="pg-field">
                    <div class="au-label-row">
                        <label class="pg-label" for="loginPassword">Password</label>
                        <a class="au-link" href="/auth/recupero-password">Password dimenticata?</a>
                    </div>
                    <div class="au-pwd">
                        <input class="pg-input" type="password" id="loginPassword" name="password" required>
                        <button class="au-pwd-toggle" type="button" onclick="togglePasswordVisibility('loginPassword', this)">Mostra</button>
                    </div>
                </div>

                <label class="au-check">
                    <input type="checkbox" name="remember" value="1">
                    <span class="au-check-box">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m20 6-11 11-5-5"></path></svg>
                    </span>
                    <span>Ricordami su questo dispositivo</span>
                </label>

                <button class="btn btn-block" data-size="lg" type="submit">Accedi</button>
            </form>

            <div class="pg-divider-or">oppure</div>
            <div class="au-social">
                <button class="au-social-btn" type="button">Continua con Google</button>
            </div>

            <p class="au-foot">Nuovo collezionista? <a href="/auth/register">Crea un account</a></p>
        </div>
    </section>

    <aside class="au-aside">
        <div class="au-aside-inner">
            <div class="au-aside-badge">NerdVault</div>
            <h2 class="au-aside-title">Il marketplace<br>per <span>collezionisti</span><br>seri.</h2>
            <p class="au-aside-desc">5.612 annunci attivi · 1.243 venditori verificati · feedback medio 4.8/5</p>
            <div class="au-aside-cards">
                <div class="au-aside-card">
                    <div class="au-product-art au-product-art-manga"><span>MNG</span></div>
                    <div class="au-aside-card-body"><strong>Cofanetto manga raro</strong><span>&euro; 89,90</span></div>
                </div>
                <div class="au-aside-card au-aside-card-shift">
                    <div class="au-product-art au-product-art-card"><span>TCG</span></div>
                    <div class="au-aside-card-body"><strong>Carta promo graded</strong><span>&euro; 1.240,00</span></div>
                </div>
            </div>
            <div class="au-aside-quote">
                <span class="au-quote-mark">"</span>
                <p>Trovato il cofanetto che cercavo da anni in 3 giorni. Spedizione perfetta.</p>
                <div class="au-quote-by"><span class="au-quote-av">G</span><span>Giulia · collezionista da 4 mesi</span></div>
            </div>
        </div>
    </aside>
</div>

{include file="layouts/footer.tpl"}
