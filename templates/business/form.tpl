{include file="layouts/header.tpl"}

<div class="card">
    <h1>Crea account business</h1>

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="business-store">

        <label for="nome_azienda">Nome azienda</label>
        <input type="text" id="nome_azienda" name="nome_azienda" value="{$post.nome_azienda|default:''}" minlength="2" maxlength="80" required>

        <label for="p_iva">Partita IVA</label>
        <input type="text" id="p_iva" name="p_iva" value="{$post.p_iva|default:''}" pattern="[0-9]{literal}{11}{/literal}" maxlength="11" inputmode="numeric" required>

        <label for="email_aziendale">Email aziendale</label>
        <input type="email" id="email_aziendale" name="email_aziendale" value="{$post.email_aziendale|default:''}" required>

        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" name="telefono" value="{$post.telefono|default:''}" pattern="\+?[0-9 ]{literal}{8,15}{/literal}">

        <fieldset>
            <legend>Indirizzo sede</legend>
            <label for="via">Via / Corso / Piazza</label>
            <input type="text" id="via" name="via" value="{$post.via|default:''}">

            <label for="numero">Numero civico</label>
            <input type="text" id="numero" name="numero" value="{$post.numero|default:''}">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" maxlength="5" pattern="[0-9]{literal}{5}{/literal}" inputmode="numeric" value="{$post.cap|default:''}">

            <label for="citta">Citta</label>
            <input type="text" id="citta" name="citta" minlength="2" maxlength="80" value="{$post.citta|default:''}">

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" pattern="[A-Za-z]{literal}{2}{/literal}" value="{$post.provincia|default:''}">
        </fieldset>

        <button class="btn" type="submit">Crea account business</button>
    </form>
</div>

{include file="layouts/footer.tpl"}
