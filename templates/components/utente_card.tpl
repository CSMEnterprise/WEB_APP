<div
    class="card clickable-card u-user-result-card"
    data-href="index.php?route=venditore&id={$utente.id_utente|default:0}"
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
        <strong>{$utente.username|default:''}</strong>
        {if !empty($utente.nome)}
            <p class="muted u-style-011">{$utente.nome}</p>
        {/if}
        <a class="btn btn-secondary u-small-profile-link"
           href="index.php?route=venditore&id={$utente.id_utente|default:0}">
            Vedi profilo
        </a>
    </div>
</div>
