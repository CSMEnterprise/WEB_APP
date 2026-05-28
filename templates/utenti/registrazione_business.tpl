{* Registrazione account business: form completo diviso in due colonne (dati aziendali + sicurezza) con una sezione separata per l'indirizzo della sede. *}
{include file="layouts/header.tpl"}

<div class="card u-style-110">
    <div class="u-style-111">
        <div class="u-style-112">ACCOUNT PROFESSIONALE</div>
        <h1 class="u-style-113">Registrazione Business</h1>
        <p class="muted u-style-094">Apri il tuo negozio su NerdVault e raggiungi migliaia di appassionati.</p>
    </div>

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="register-business-post">
        <input type="hidden" name="_business_registration" value="1">

        <div class="grid-2 u-style-114">
            <div class="u-style-115">
                <h3 class="u-style-116">Profilo business</h3>

                <label for="nome_azienda">Nome azienda</label>
                <input type="text" id="nome_azienda" name="nome_azienda" value="{$post.nome_azienda|default:''}" minlength="2" maxlength="80" required>

                <label for="p_iva">Partita IVA</label>
                <input type="text" id="p_iva" name="p_iva" value="{$post.p_iva|default:''}" pattern="[0-9]{literal}{11}{/literal}" maxlength="11" inputmode="numeric" required>

                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" value="{$post.telefono|default:''}" pattern="\+?[0-9 ]{literal}{8,15}{/literal}" required>
            </div>

            <div class="u-style-115">
                <h3 class="u-style-116">Sicurezza e accesso</h3>

                <label for="email_aziendale">Email aziendale</label>
                <input type="email" id="email_aziendale" name="email_aziendale" value="{$post.email_aziendale|default:''}" required>

                <label for="businessPassword">Password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="businessPassword" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPassword', this)">Mostra</button>
                </div>

                <label for="businessPasswordConfirm">Conferma password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="businessPasswordConfirm" name="password_confirm" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('businessPasswordConfirm', this)">Mostra</button>
                </div>
            </div>
        </div>

        <div class="u-style-118">
            <h3 class="u-style-116">Indirizzo sede</h3>
            <div class="grid-2 u-style-119">
                <div>
                    <label for="via">Via / Corso / Piazza</label>
                    <input type="text" id="via" name="via" value="{$post.via|default:''}">
                </div>
                <div>
                    <label for="numero">Numero civico</label>
                    <input type="text" id="numero" name="numero" value="{$post.numero|default:''}">
                </div>
                <div>
                    <label for="cap">CAP</label>
                    <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{literal}{5}{/literal}" inputmode="numeric" value="{$post.cap|default:''}">
                </div>
                <div>
                    <label for="citta">Citta</label>
                    <input type="text" id="citta" name="citta" minlength="2" maxlength="80" value="{$post.citta|default:''}">
                </div>
                <div>
                    <label for="provincia">Provincia</label>
                    <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{literal}{2}{/literal}" value="{$post.provincia|default:''}">
                </div>
            </div>
        </div>

        <div class="u-style-120">
            <button class="btn u-style-121" type="submit">Crea account business</button>
            <p class="u-style-122">
                Vuoi scegliere un altro tipo di account? <a class="u-style-107" href="index.php?route=register">Scelta registrazione</a><br>
                Hai gia un account? <a class="u-style-107" href="index.php?route=login">Accedi qui</a>
            </p>
        </div>
    </form>
</div>

{include file="layouts/footer.tpl"}
