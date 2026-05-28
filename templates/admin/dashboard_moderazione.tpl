{* Dashboard moderazione globale: tabella di tutte le azioni compiute da tutti gli admin, filtrabile per email o ID admin. Solo gli admin di livello 2 possono accedere. *}
{include file="layouts/header.tpl"}

<h1>Dashboard moderazione</h1>

<section class="card">
    <h2>Ricerca e filtri</h2>
    <form method="get" action="index.php">
        <input type="hidden" name="route" value="admin-dashboard">
        <label for="admin">Admin</label>
        <input type="search" id="admin" name="admin" placeholder="Cerca per email o ID admin" value="{$filters.admin|default:''}">
        <button class="btn" type="submit">Filtra</button>
        <a class="btn btn-secondary" href="index.php?route=admin-dashboard">Reset</a>
    </form>
</section>

<section class="u-style-001">
    <h2>Azioni di moderazione</h2>
    {if !empty($azioniModerazione)}
        <table>
            <thead>
                <tr><th>ID</th><th>Admin</th><th>Livello</th><th>Azione</th><th>Riferimenti</th><th>Data</th></tr>
            </thead>
            <tbody>
                {foreach $azioniModerazione as $azione}
                    <tr>
                        <td>{$azione.id_moderazione|default:''}</td>
                        <td>#{$azione.id_admin|default:''}<br><span class="muted">{$azione.admin_email|default:''}</span></td>
                        <td>{$azione.livello_sicurezza|default:''}</td>
                        <td>{$azione.azione_compiuta|default:''}</td>
                        <td>
                            {if !empty($azione.id_utente)}Utente #{$azione.id_utente} {/if}
                            {if !empty($azione.id_feedback)}Feedback #{$azione.id_feedback} {/if}
                            {if !empty($azione.id_annuncio)}Annuncio #{$azione.id_annuncio} {/if}
                            {if !empty($azione.id_business)}Business #{$azione.id_business} {/if}
                            {if empty($azione.id_utente) && empty($azione.id_feedback) && empty($azione.id_annuncio) && empty($azione.id_business)}-{/if}
                        </td>
                        <td>{$azione.data_azione|default:''}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <div class="card"><p>Nessuna azione di moderazione trovata.</p></div>
    {/if}
</section>

{include file="layouts/footer.tpl"}
