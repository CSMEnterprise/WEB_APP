{* Profilo feedback pubblico di un venditore: media con badge stella in cima, poi lista di tutte le recensioni con link all'annuncio collegato. *}
{include file="layouts/header.tpl"}

<h1>Feedback ricevuti</h1>

{if isset($media) && $media > 0}
    <div class="card u-style-016">
        <span class="u-style-017">&#9733;</span>
        <div>
            <strong class="u-style-018">{$media|number_format:1:",":"."} / 5</strong>
            <p class="muted u-style-019">{$feedback|count_items} recensioni</p>
        </div>
    </div>
{/if}

{if !empty($feedback)}
    {foreach $feedback as $item}
        <div class="card">
            <div class="u-style-020">
                <strong>{$item.autore|default:''}</strong>
                <span class="u-style-021">{include file="components/stars.tpl" value=$item.valutazione|default:0}</span>
            </div>
            <p class="muted u-style-004">
                Annuncio: <a href="index.php?route=annuncio&id={$item.annuncio_id|default:0}">{$item.annuncio_titolo|default:''}</a>
            </p>
            {if !empty($item.commento)}<p>{$item.commento}</p>{/if}
            <p class="muted u-style-022">{$item.data_feedback|default:''}</p>
        </div>
    {/foreach}
{else}
    <div class="card"><p>Nessun feedback ricevuto.</p></div>
{/if}

{include file="layouts/footer.tpl"}
