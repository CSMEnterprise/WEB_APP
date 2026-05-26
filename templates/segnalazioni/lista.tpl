{include file="layouts/header.tpl"}

<h1>Segnalazioni</h1>

{if !empty($segnalazioni)}
    {foreach $segnalazioni as $segnalazione}
        <div class="card">
            <h2>{$segnalazione.tipologia|default:''}</h2>
            {if !empty($segnalazione.descrizione)}<p>{$segnalazione.descrizione}</p>{/if}
            <p><strong>Stato:</strong> {$segnalazione.stato|default:''}</p>
            <p class="muted">{$segnalazione.data_segnalazione|default:''}</p>
        </div>
    {/foreach}
{else}
    <div class="card"><p>Nessuna segnalazione presente.</p></div>
{/if}

{include file="layouts/footer.tpl"}
