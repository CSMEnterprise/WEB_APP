{* Registrazione account PRO: form reale nello stile auth di NerdVault Pages.html. *}
{include file="layouts/header.tpl"}

<div class="au-layout">
    <section class="au-form-side au-form-side-wide">
        <div class="au-tabs" role="tablist" aria-label="Registrazione">
            <a class="au-tab" href="/auth/login">Accedi</a>
            <a class="au-tab is-active" href="/auth/register">Registrati</a>
            <span class="au-tab-bg au-tab-bg-right"></span>
        </div>

        <div class="au-panel">
            <a class="au-back" href="/auth/register">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
                Indietro
            </a>

            <h1 class="au-title">Account <span class="au-title-gold">PRO</span></h1>
            <p class="au-sub">Compila i dati della tua attivita per aprire una vetrina venditore su NerdVault.</p>

            {if !empty($errore)}
                <div class="pg-alert" data-tone="danger">{$errore}</div>
            {/if}

            <form class="au-form" method="post" action="/auth/register-business">
                <input type="hidden" name="_business_registration" value="1">

                <div class="au-form-section">
                    <h3 class="au-form-section-title">Profilo business</h3>
                    <div class="pg-field">
                        <label class="pg-label" for="nome_azienda">Nome azienda</label>
                        <input class="pg-input" type="text" id="nome_azienda" name="nome_azienda" value="{$post.nome_azienda|default:''}" placeholder="es. MangaVault srl" minlength="2" maxlength="80" required>
                    </div>

                    <div class="pg-field-row">
                        <div class="pg-field">
                            <label class="pg-label" for="p_iva">Partita IVA</label>
                            <input class="pg-input" type="text" id="p_iva" name="p_iva" value="{$post.p_iva|default:''}" placeholder="01234567890" pattern="[0-9]{literal}{11}{/literal}" maxlength="11" inputmode="numeric" required>
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="telefono">Telefono</label>
                            <input class="pg-input" type="text" id="telefono" name="telefono" value="{$post.telefono|default:''}" placeholder="+39 ..." pattern="\+?[0-9 ]{literal}{8,15}{/literal}" required>
                        </div>
                    </div>
                </div>

                <div class="au-form-section">
                    <h3 class="au-form-section-title">Sicurezza e accesso</h3>
                    <div class="pg-field">
                        <label class="pg-label" for="email_aziendale">Email aziendale</label>
                        <input class="pg-input" type="email" id="email_aziendale" name="email_aziendale" value="{$post.email_aziendale|default:''}" placeholder="negozio@example.com" required>
                    </div>

                    <div class="pg-field-row">
                        <div class="pg-field">
                            <label class="pg-label" for="businessPassword">Password</label>
                            <div class="au-pwd">
                                <input class="pg-input" type="password" id="businessPassword" name="password" placeholder="Minimo 10 caratteri" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                                <button class="au-pwd-toggle" type="button" onclick="togglePasswordVisibility('businessPassword', this)">Mostra</button>
                            </div>
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="businessPasswordConfirm">Conferma password</label>
                            <div class="au-pwd">
                                <input class="pg-input" type="password" id="businessPasswordConfirm" name="password_confirm" placeholder="Ripeti la password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                                <button class="au-pwd-toggle" type="button" onclick="togglePasswordVisibility('businessPasswordConfirm', this)">Mostra</button>
                            </div>
                        </div>
                    </div>
                    <div class="au-strength">
                        <div class="au-strength-bar"><div></div></div>
                        <span>Per gli account PRO serve una password forte: maiuscola, simbolo e 10 caratteri.</span>
                    </div>
                </div>

                <div class="au-form-section">
                    <h3 class="au-form-section-title">Indirizzo sede</h3>
                    <div class="pg-field-row">
                        <div class="pg-field">
                            <label class="pg-label" for="via">Via / Corso / Piazza</label>
                            <input class="pg-input" type="text" id="via" name="via" value="{$post.via|default:''}">
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="numero">Numero civico</label>
                            <input class="pg-input" type="text" id="numero" name="numero" value="{$post.numero|default:''}">
                        </div>
                    </div>
                    <div class="pg-field-row">
                        <div class="pg-field">
                            <label class="pg-label" for="cap">CAP</label>
                            <input class="pg-input" type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{literal}{5}{/literal}" inputmode="numeric" value="{$post.cap|default:''}">
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="citta">Citta</label>
                            <input class="pg-input" type="text" id="citta" name="citta" minlength="2" maxlength="80" value="{$post.citta|default:''}">
                        </div>
                    </div>
                    <div class="pg-field">
                        <label class="pg-label" for="provincia">Provincia</label>
                        <input class="pg-input" type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{literal}{2}{/literal}" value="{$post.provincia|default:''}">
                    </div>
                </div>

                <label class="au-check">
                    <input type="checkbox" required>
                    <span class="au-check-box">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m20 6-11 11-5-5"></path></svg>
                    </span>
                    <span>Confermo che i dati aziendali sono corretti e accetto i <a class="au-link" href="/legale/termini">Termini</a>.</span>
                </label>

                <button class="btn btn-block" data-size="lg" type="submit">Crea account PRO</button>
            </form>

            <p class="au-foot">Hai gia un account? <a href="/auth/login">Accedi</a></p>
        </div>
    </section>

    <aside class="au-aside">
        <div class="au-aside-inner">
            <div class="au-aside-badge">Venditori PRO</div>
            <h2 class="au-aside-title">La tua vetrina<br>da venditore<br><span>verificato.</span></h2>
            <p class="au-aside-desc">Badge PRO, profilo aziendale, ordini ricevuti e priorita nella fiducia degli acquirenti.</p>
            <div class="au-aside-cards">
                <div class="au-aside-card">
                    <div class="au-product-art au-product-art-pro"><span>PRO</span></div>
                    <div class="au-aside-card-body"><strong>Vetrina brandizzata</strong><span>Profilo negozio</span></div>
                </div>
                <div class="au-aside-card au-aside-card-shift">
                    <div class="au-product-art au-product-art-figure"><span>FIG</span></div>
                    <div class="au-aside-card-body"><strong>Annunci professionali</strong><span>Badge PRO</span></div>
                </div>
            </div>
            <div class="au-aside-quote">
                <span class="au-quote-mark">"</span>
                <p>Una pagina pensata per vendere con continuita, non solo per pubblicare un annuncio ogni tanto.</p>
                <div class="au-quote-by"><span class="au-quote-av">P</span><span>Account PRO NerdVault</span></div>
            </div>
        </div>
    </aside>
</div>

{include file="layouts/footer.tpl"}
