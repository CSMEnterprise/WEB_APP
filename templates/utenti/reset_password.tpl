{include file="layouts/header.tpl"}

<div class="u-style-080">
    <div class="card u-style-081">
        <div class="u-style-082">
            <div class="u-style-083">Password</div>
            <h1 class="u-style-084">Nuova password</h1>
            <p class="muted">Scegli una password sicura per il tuo account.</p>
        </div>

        {if !empty($errore)}
            <div class="alert alert-error">{$errore}</div>
            {if ($idUtente|default:0) == 0}
                <p class="u-style-125"><a href="index.php?route=recupero-password" class="btn btn-secondary">Richiedi nuovo link</a></p>
            {/if}
        {/if}

        {if ($idUtente|default:0) > 0}
            <form method="post" action="index.php?route=reset-password-post">
                <input type="hidden" name="token" value="{$token|default:''}">

                <label for="newPassword">Nuova password</label>
                <div class="password-wrapper">
                    <input type="password" id="newPassword" name="password" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('newPassword', this)">Mostra</button>
                </div>

                <label for="newPasswordConfirm">Conferma password</label>
                <div class="password-wrapper">
                    <input type="password" id="newPasswordConfirm" name="password_confirm" pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{literal}{10,}{/literal}" autocomplete="new-password" required>
                    <button class="btn btn-secondary btn-password-toggle" type="button" onclick="togglePasswordVisibility('newPasswordConfirm', this)">Mostra</button>
                </div>

                <button type="submit" class="btn u-style-089">Salva nuova password</button>
            </form>
        {/if}
    </div>
</div>

{include file="layouts/footer.tpl"}
