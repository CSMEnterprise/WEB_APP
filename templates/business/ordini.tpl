{* Lista ordini ricevuti dall'account business: tabella con ID pagamento, titolo annuncio, importo, stato e data. *}
{include file="layouts/header.tpl"}

<h1>Ordini ricevuti</h1>

{if !empty($ordini)}
    <table>
        <thead>
            <tr><th>ID</th><th>Annuncio</th><th>Importo</th><th>Stato</th><th>Data</th></tr>
        </thead>
        <tbody>
            {foreach $ordini as $ordine}
                <tr>
                    <td>{$ordine.id_pagamento|default:''}</td>
                    <td>{$ordine.titolo|default:''}</td>
                    <td>&euro; {$ordine.importo_totale|default:0|number_format:2:",":"."}</td>
                    <td>{$ordine.stato|default:''}</td>
                    <td>{$ordine.data|default:''}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="card"><p>Non ci sono ordini ricevuti.</p></div>
{/if}

{include file="layouts/footer.tpl"}
