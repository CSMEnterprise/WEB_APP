{* Recupero password: form richiesta link di reset via email. In modalità debug (senza SMTP configurato) il link viene mostrato direttamente nella pagina. *}
{include file="layouts/header.tpl"}

<div class="u-style-080">
    <div class="card u-style-081">
        <div class="u-style-082">
            <div class="u-style-083">Reset</div>
            <h1 class="u-style-084">Password dimenticata?</h1>
            <p class="muted">Inserisci la tua email e ti invieremo un link per reimpostarla.</p>
        </div>

        {if !empty($successo)}
            <div class="alert alert-success">{$successo}</div>
            {if !empty($debugLink)}
                <div class="u-style-085">
                    <p class="u-style-086">Modalita debug - Link di reset</p>
                    <p class="u-style-087">L'email non viene inviata in modalita debug. Clicca direttamente il link qui sotto:</p>
                    <a class="u-debug-link" href="{$debugLink}">{$debugLink}</a>
                </div>
            {/if}
            <p class="u-style-088"><a href="index.php?route=login" class="btn btn-secondary">Torna al login</a></p>
        {else}
            {if !empty($errore)}<div class="alert alert-error">{$errore}</div>{/if}
            <form method="post" action="index.php?route=recupero-password-post">
                <label for="email">La tua email</label>
                <input type="email" id="email" name="email" value="{$post.email|default:''}" placeholder="tuaemail@esempio.it" required>
                <button type="submit" class="btn u-style-089">Invia link di recupero</button>
            </form>
            <p class="u-style-090"><a class="u-style-091" href="index.php?route=login">Torna al login</a></p>
        {/if}
    </div>
</div>

{include file="layouts/footer.tpl"}
