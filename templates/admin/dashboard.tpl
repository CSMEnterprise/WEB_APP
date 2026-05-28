{* Dashboard personale dell'admin: statistiche globali della piattaforma e log delle azioni di moderazione eseguite dall'admin corrente. *}
{include file="layouts/header.tpl"}

<h1>Profilo admin</h1>

{* Riquadri con i contatori principali della piattaforma *}
<section class="grid">
    <article class="card"><h2>Utenti</h2><p class="price">{$stats.totUtenti|default:0}</p></article>
    <article class="card"><h2>Annunci</h2><p class="price">{$stats.totAnnunci|default:0}</p></article>
    <article class="card"><h2>Segnalazioni aperte</h2><p class="price">{$stats.totSegnalazioni|default:0}</p></article>
    <article class="card"><h2>Pagamenti</h2><p class="price">{$stats.totPagamenti|default:0}</p></article>
</section>

<p>
    <a class="btn" href="index.php?route=admin-utenti">Gestisci utenti</a>
    <a class="btn btn-secondary" href="index.php?route=admin-segnalazioni">Gestisci segnalazioni</a>
</p>

{* Log delle sole azioni compiute dall'admin attualmente loggato *}
<section class="u-style-001">
    <h2>Azioni eseguite da te</h2>

    {if !empty($azioniModera)}
        <table>
            <thead>
                <tr><th>Azione</th><th>Riferimenti</th><th>Data</th></tr>
            </thead>
            <tbody>
                {foreach $azioniModera as $azione}
                    <tr>
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
        <div class="card"><p>Non hai ancora azioni di moderazione registrate.</p></div>
    {/if}
</section>

{include file="layouts/footer.tpl"}
