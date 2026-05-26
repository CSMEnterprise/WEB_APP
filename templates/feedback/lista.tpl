{include file="layouts/header.tpl"}

<h1>Feedback</h1>

{if !empty($feedback)}
    {foreach $feedback as $item}
        <div class="card">
            <h2>{$item.titolo|default:'Feedback'}</h2>
            <p><strong>Autore:</strong> {$item.autore|default:''}</p>
            <p><strong>Valutazione:</strong> {include file="components/stars.tpl" value=$item.valutazione|default:0}</p>
            {if !empty($item.commento)}<p>{$item.commento}</p>{/if}
            <p class="muted">{$item.data_feedback|default:''}</p>
        </div>
    {/foreach}
{else}
    <div class="card"><p>Non sono presenti feedback.</p></div>
{/if}

{include file="layouts/footer.tpl"}
