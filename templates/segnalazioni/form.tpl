{* Form invio segnalazione: l'oggetto segnalato (annuncio/utente/business/feedback) viene passato tramite campi hidden popolati dal GET. L'utente sceglie solo tipologia e descrizione. *}
{include file="layouts/header.tpl"}

<div class="card">
    <h1>Invia segnalazione</h1>

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="/segnalazione/store">
                <input type="hidden" name="id_annuncio" value="{$get.id_annuncio|default:$post.id_annuncio|default:''}">
        <input type="hidden" name="id_utente_segnalato" value="{$get.id_utente_segnalato|default:$post.id_utente_segnalato|default:''}">
        <input type="hidden" name="id_business" value="{$get.id_business|default:$post.id_business|default:''}">
        <input type="hidden" name="id_feedback" value="{$get.id_feedback|default:$post.id_feedback|default:''}">

        <label for="tipologia">Tipologia</label>
        <select id="tipologia" name="tipologia" required>
            <option value="Spam" {if ($post.tipologia|default:'') == 'Spam'}selected{/if}>Spam</option>
            <option value="Truffa" {if ($post.tipologia|default:'') == 'Truffa'}selected{/if}>Truffa</option>
            <option value="Contenuto_inappropriato" {if ($post.tipologia|default:'') == 'Contenuto_inappropriato'}selected{/if}>Contenuto inappropriato</option>
            <option value="Altro" {if ($post.tipologia|default:'') == 'Altro'}selected{/if}>Altro</option>
        </select>

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione">{$post.descrizione|default:''}</textarea>

        <button class="btn" type="submit">Invia segnalazione</button>
    </form>
</div>

{include file="layouts/footer.tpl"}
