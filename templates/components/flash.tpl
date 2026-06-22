{* Componente messaggi flash: mostra alert di errore e/o successo globali. Va incluso nelle pagine che possono ricevere feedback da un redirect. *}
{if !empty($errore)}
    <div class="alert alert-error">{$errore}</div>
{/if}

{if !empty($successo)}
    <div class="alert alert-success">{$successo}</div>
{/if}
