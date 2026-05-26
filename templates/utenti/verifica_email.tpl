{include file="layouts/header.tpl"}

<div class="u-style-080">
    <div class="card u-style-126">
        {if !empty($successo)}
            <div class="u-style-127">OK</div>
            <h1 class="u-style-128">Email verificata!</h1>
            <p class="muted u-style-129">Il tuo account e ora attivo. Puoi accedere.</p>
            <a href="index.php?route=login" class="btn">Vai al login</a>
        {else}
            <div class="u-style-127">Errore</div>
            <h1 class="u-style-128">Verifica fallita</h1>
            <div class="alert alert-error">{$errore|default:'Errore sconosciuto.'}</div>
            <p class="u-style-038">
                <a href="index.php?route=verifica-email-attesa" class="btn btn-secondary">Reinvia email</a>
                <a href="index.php?route=login" class="btn btn-secondary">Login</a>
            </p>
        {/if}
    </div>
</div>

{include file="layouts/footer.tpl"}
