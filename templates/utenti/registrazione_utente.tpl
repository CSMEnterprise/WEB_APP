{* Registrazione utente normale: form diviso in due colonne (dati profilo + sicurezza) con username, nome opzionale, telefono, email e password con conferma. *}
{include file="layouts/header.tpl"}

<div class="card u-style-123">
    <div class="u-style-111">
        <div class="u-style-124">ACCOUNT PERSONALE</div>
        <h1 class="u-style-113">Registrazione Utente</h1>
        <p class="muted u-style-094">Crea un account personale per acquistare, vendere e salvare annunci su NerdVault.</p>
    </div>

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="/auth/register-user">
                <div class="grid-2 u-style-114">
            <div class="u-style-115">
                <h3 class="u-style-116">Profilo utente</h3>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{$post.username|default:''}" pattern="[A-Za-z0-9_.-]{literal}{3,30}{/literal}" minlength="3" maxlength="30" required>

                <label for="nome">Nome completo (opzionale)</label>
                <input type="text" id="nome" name="nome" value="{$post.nome|default:''}" minlength="2" maxlength="50">

                <label for="telefono">Telefono</label>
                <input type="text" id="telefono" name="telefono" value="{$post.telefono|default:''}" pattern="\+?[0-9 ]{literal}{8,15}{/literal}" required>
            </div>

            <div class="u-style-115">
                <h3 class="u-style-116">Sicurezza e accesso</h3>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{$post.email|default:''}" required>

                <label for="userRegisterPassword">Password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="userRegisterPassword" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPassword', this)">Mostra</button>
                </div>

                <label for="userRegisterPasswordConfirm">Conferma password</label>
                <div class="password-wrapper u-style-117">
                    <input type="password" id="userRegisterPasswordConfirm" name="password_confirm" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('userRegisterPasswordConfirm', this)">Mostra</button>
                </div>
            </div>
        </div>

        <div class="u-style-120">
            <button class="btn u-style-121" type="submit">Crea account utente</button>
            <p class="u-style-122">
                Vuoi scegliere un altro tipo di account? <a class="u-style-102" href="/auth/register">Scelta registrazione</a><br>
                Hai gia un account? <a class="u-style-102" href="/auth/login">Accedi qui</a>
            </p>
        </div>
    </form>
</div>

{include file="layouts/footer.tpl"}
