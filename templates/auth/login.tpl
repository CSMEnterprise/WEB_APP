{* Pagina login: form email/password con link a recupero password. Mostra un alert specifico se l'email non è ancora verificata, con link al reinvio. *}
{include file="layouts/header.tpl"}

<div class="u-style-048">
    <div class="card u-style-049">

        <div class="u-style-050"></div>
        <div class="u-style-051"></div>

        <div class="u-style-052">
            <div class="u-style-053">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            </div>
            <h1 class="u-style-054">Bentornato</h1>
            <p class="u-style-055">Accedi per continuare in NerdVault</p>
        </div>

        {if !empty($resetOk)}
            <div class="alert alert-success u-style-056">
                Password aggiornata! Ora puoi accedere.
            </div>
        {/if}

        {if !empty($errore)}
            {if !empty($isEmailNonVerificata)}
                <div class="alert alert-error u-style-057">
                    <strong>Email non verificata.</strong><br>
                    Controlla la tua casella oppure
                    <a href="/auth/verifica-email-attesa?email={$emailNonVerificataUrl nofilter}"
                       class="u-style-140">richiedi un nuovo link</a>.
                </div>
            {else}
                <div class="alert alert-error u-style-058">{$errore}</div>
            {/if}
        {/if}

        <form class="u-style-059" method="post" action="/auth/login">
                        <div class="u-style-060">
                <label class="u-style-061" for="email">Email</label>
                <input class="u-style-062" type="email" id="email" name="email" required>
            </div>

            <div class="u-style-002">
                <div class="u-style-063">
                    <label class="u-style-064" for="loginPassword">Password</label>
                    <a class="u-style-065" href="/auth/recupero-password">Dimenticata?</a>
                </div>
                <div class="u-style-066">
                    <input class="u-style-067" type="password" id="loginPassword" name="password" required>
                    <button class="u-style-068" type="button" onclick="togglePasswordVisibility('loginPassword', this)">Mostra</button>
                </div>
            </div>

            <button class="u-style-069" type="submit">
                Accedi ora
            </button>
        </form>

        <div class="u-style-070">
            <p class="u-style-071">Nuovo esploratore? <a class="u-style-072" href="/auth/register">Crea un account</a></p>
        </div>
    </div>
</div>

{include file="layouts/footer.tpl"}
