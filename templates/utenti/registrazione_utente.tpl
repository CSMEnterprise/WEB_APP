{* Registrazione account collezionista: form reale nello stile auth di NerdVault Pages.html. *}
{include file="layouts/header.tpl"}

<div class="au-layout">
    <section class="au-form-side">
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

            <h1 class="au-title">Account Collezionista</h1>
            <p class="au-sub">Pochi dati e sei dentro: compra, vendi occasionalmente e salva i pezzi che ami.</p>

            {if !empty($errore)}
                <div class="pg-alert" data-tone="danger">{$errore}</div>
            {/if}

            <form class="au-form" method="post" action="/auth/register-user">
                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                <div class="pg-field-row">
                    <div class="pg-field">
                        <label class="pg-label" for="username">Username</label>
                        <input class="pg-input" type="text" id="username" name="username" value="{$post.username|default:''}" placeholder="es. marco_collector" pattern="[A-Za-z0-9_.-]{literal}{3,30}{/literal}" minlength="3" maxlength="30" required>
                    </div>
                    <div class="pg-field">
                        <label class="pg-label" for="email">Email</label>
                        <input class="pg-input" type="email" id="email" name="email" value="{$post.email|default:''}" placeholder="tu@example.com" required>
                    </div>
                </div>

                <div class="pg-field-row">
                    <div class="pg-field">
                        <label class="pg-label" for="nome">Nome completo</label>
                        <input class="pg-input" type="text" id="nome" name="nome" value="{$post.nome|default:''}" placeholder="Nome e cognome" minlength="2" maxlength="50">
                    </div>
                    <div class="pg-field">
                        <label class="pg-label" for="telefono">Telefono</label>
                        <input class="pg-input" type="text" id="telefono" name="telefono" value="{$post.telefono|default:''}" placeholder="+39 ..." pattern="\+?[0-9 ]{literal}{8,15}{/literal}" required>
                    </div>
                </div>

                <div class="pg-field">
                    <label class="pg-label" for="userRegisterPassword">Password</label>
                    <div class="au-pwd">
                        <input class="pg-input" type="password" id="userRegisterPassword" name="password" placeholder="Minimo 10 caratteri" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                        <button class="au-pwd-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPassword', this)">Mostra</button>
                    </div>
                    <div class="au-strength">
                        <div class="au-strength-bar"><div></div></div>
                        <span>Usa almeno una maiuscola, un simbolo e 10 caratteri.</span>
                    </div>
                </div>

                <div class="pg-field">
                    <label class="pg-label" for="userRegisterPasswordConfirm">Conferma password</label>
                    <div class="au-pwd">
                        <input class="pg-input" type="password" id="userRegisterPasswordConfirm" name="password_confirm" placeholder="Ripeti la password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                        <button class="au-pwd-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPasswordConfirm', this)">Mostra</button>
                    </div>
                </div>

                <label class="au-check">
                    <input type="checkbox" required>
                    <span class="au-check-box">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m20 6-11 11-5-5"></path></svg>
                    </span>
                    <span>Accetto i <a class="au-link" href="/legale/termini">Termini di servizio</a> e la <a class="au-link" href="/legale/privacy">Privacy</a></span>
                </label>

                <button class="btn btn-block" data-size="lg" type="submit">Crea account</button>
            </form>

            <p class="au-foot">Hai gia un account? <a href="/auth/login">Accedi</a></p>
        </div>
    </section>

    <aside class="au-aside">
        <div class="au-aside-inner">
            <div class="au-aside-badge">Collezionista</div>
            <h2 class="au-aside-title">Il tuo vault<br>personale<br><span>inizia qui.</span></h2>
            <p class="au-aside-desc">Wishlist, carrello, feedback verificati e annunci occasionali in un profilo unico.</p>
            <div class="au-aside-cards">
                <div class="au-aside-card">
                    <div class="au-product-art au-product-art-manga"><span>MNG</span></div>
                    <div class="au-aside-card-body"><strong>Wishlist manga</strong><span>Salva pezzi rari</span></div>
                </div>
                <div class="au-aside-card au-aside-card-shift">
                    <div class="au-product-art au-product-art-card"><span>TCG</span></div>
                    <div class="au-aside-card-body"><strong>Carte e figure</strong><span>Compra sicuro</span></div>
                </div>
            </div>
            <div class="au-aside-quote">
                <span class="au-quote-mark">"</span>
                <p>Un account leggero per chi compra, vende ogni tanto e vuole tenere tutto in ordine.</p>
                <div class="au-quote-by"><span class="au-quote-av">C</span><span>Account collezionista</span></div>
            </div>
        </div>
    </aside>
</div>

{include file="layouts/footer.tpl"}
