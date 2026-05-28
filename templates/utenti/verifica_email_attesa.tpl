{* Pagina di attesa verifica email: mostrata subito dopo la registrazione. Include istruzioni, form di reinvio e (solo in modalità debug) il link di verifica diretto. *}
{include file="layouts/header.tpl"}

<div class="u-style-080">
    <div class="card u-style-130">
        <div class="u-style-127">Email</div>
        <h1 class="u-style-128">Controlla la tua email</h1>

        {if !empty($successo)}
            <div class="alert alert-success">{$successo}</div>
        {elseif !empty($errore)}
            <div class="alert alert-error">{$errore}</div>
        {else}
            <p class="muted u-style-131">
                Abbiamo inviato un link di verifica a
                {if ($email|default:'') != ''}<strong class="u-style-132">{$email}</strong>{else}il tuo indirizzo email{/if}.
            </p>
            <p class="muted u-style-133">Clicca il link nell'email per attivare il tuo account. Il link scadra tra <strong class="u-style-132">48 ore</strong>.</p>
        {/if}

        {if !empty($debugLink)}
            <div class="u-style-134">
                <p class="u-style-086">Modalita debug - Link di verifica</p>
                <p class="u-style-087">L'email non viene inviata in modalita debug. Clicca direttamente il link qui sotto:</p>
                <a class="u-debug-link" href="{$debugLink}">{$debugLink}</a>
            </div>
        {/if}

        <details class="u-style-135">
            <summary class="u-style-136">Non hai ricevuto l'email?</summary>
            <form class="u-style-137" method="post" action="index.php?route=reinvia-verifica">
                <label for="reinvia_email">La tua email</label>
                <input type="email" id="reinvia_email" name="email" value="{$email|default:''}" required placeholder="tuaemail@esempio.it">
                <button type="submit" class="btn u-style-138">Reinvia email di verifica</button>
            </form>
        </details>

        <p class="u-style-139"><a href="index.php?route=login" class="btn btn-secondary">Torna al login</a></p>
    </div>
</div>

{include file="layouts/footer.tpl"}
