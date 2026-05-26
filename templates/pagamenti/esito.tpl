{include file="layouts/header.tpl"}

<div class="card">
    <h1>Esito pagamento</h1>

    {if ($status|default:'') == 'ok'}
        <div class="alert alert-success">Pagamento completato correttamente!</div>
        {if $numeroPagamenti|default:0 > 1}
            <p>Sono stati completati {$numeroPagamenti} pagamenti.</p>
        {/if}
        <p>Vuoi lasciare un feedback al venditore?</p>
        <a class="btn" href="index.php?route=feedback-create&id_pagamento={$idPagamento|default:0}">Lascia un feedback</a>
        <a class="btn btn-secondary" href="index.php?route=home">Torna agli annunci</a>
    {else}
        <div class="alert alert-error">Pagamento non completato.</div>
        <a class="btn" href="index.php?route=home">Torna agli annunci</a>
    {/if}
</div>

{include file="layouts/footer.tpl"}
