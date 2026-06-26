{* Componente card utente: mostra avatar, identita pubblica e link al profilo venditore. Usato nei risultati di ricerca testuale. *}
{assign var=isBusinessResult value=!empty($utente.id_acc_business)}
{if $isBusinessResult && !empty($utente.nome_azienda)}
    {assign var=displayName value=$utente.nome_azienda}
{else}
    {assign var=displayName value=$utente.username|default:''}
{/if}
<div
    class="card clickable-card u-user-result-card"
    data-href="/utente/venditore/{$utente.id_utente|default:0}"
    role="link"
    tabindex="0">
    <div class="u-style-009">
        {if !empty($utente.propic)}
            <img class="u-fill-image" src="{$utente.propic}" alt="Foto profilo">
        {else}
            <span class="u-style-010">&#128100;</span>
        {/if}
    </div>
    <div>
        <strong>{$displayName}</strong>
        {if $isBusinessResult}
            <p class="muted u-style-011"><span class="nv-pro-badge">PRO</span></p>
        {elseif !empty($utente.nome)}
            <p class="muted u-style-011">{$utente.nome}</p>
        {/if}
        <a class="btn btn-secondary u-small-profile-link"
           href="/utente/venditore/{$utente.id_utente|default:0}">
            Vedi profilo
        </a>
    </div>
</div>
