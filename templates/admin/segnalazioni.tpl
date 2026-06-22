{* Gestione segnalazioni admin: lista filtrabile per oggetto (annuncio/utente/business/feedback) e tipologia. Permette di chiudere o eliminare ogni segnalazione. *}
{include file="layouts/header.tpl"}

<h1>Gestione segnalazioni</h1>

<section class="card">
    <h2>Filtri</h2>
    <form method="get" action="/admin/segnalazioni">
                <label for="oggetto">Oggetto segnalato</label>
        <select id="oggetto" name="oggetto">
            <option value="">Tutti</option>
            <option value="annuncio" {if ($filters.oggetto|default:'') == 'annuncio'}selected{/if}>Annuncio</option>
            <option value="utente" {if ($filters.oggetto|default:'') == 'utente'}selected{/if}>Utente</option>
            <option value="business" {if ($filters.oggetto|default:'') == 'business'}selected{/if}>Business</option>
            <option value="feedback" {if ($filters.oggetto|default:'') == 'feedback'}selected{/if}>Feedback</option>
        </select>

        <label for="tipologia">Tipologia</label>
        <select id="tipologia" name="tipologia">
            <option value="">Tutte</option>
            <option value="Spam" {if ($filters.tipologia|default:'') == 'Spam'}selected{/if}>Spam</option>
            <option value="Truffa" {if ($filters.tipologia|default:'') == 'Truffa'}selected{/if}>Truffa</option>
            <option value="Contenuto_inappropriato" {if ($filters.tipologia|default:'') == 'Contenuto_inappropriato'}selected{/if}>Contenuto inappropriato</option>
            <option value="Altro" {if ($filters.tipologia|default:'') == 'Altro'}selected{/if}>Altro</option>
        </select>

        <button class="btn" type="submit">Filtra</button>
        <a class="btn btn-secondary" href="/admin/segnalazioni">Reset</a>
    </form>
</section>

{if !empty($segnalazioni)}
    <table>
        <thead>
            <tr><th>ID</th><th>Segnalante</th><th>Tipologia</th><th>Descrizione</th><th>Oggetto segnalato</th><th>Stato</th><th>Data</th><th>Azioni</th></tr>
        </thead>
        <tbody>
            {foreach $segnalazioni as $s}
                <tr>
                    <td>{$s.id_segnalazione|default:''}</td>
                    <td>{$s.segnalante_username|default:''}</td>
                    <td>{$s.tipologia|default:''}</td>
                    <td>{$s.descrizione|default:'-'}</td>
                    <td>
                        {if !empty($s.id_annuncio)}
                            Annuncio: {if !empty($s.annuncio_titolo)}{$s.annuncio_titolo}{else}#{$s.id_annuncio}{/if}<br>
                            <a class="btn btn-secondary u-admin-object-link" href="/annuncio/show/{$s.id_annuncio}" target="_blank">Vai all'oggetto</a>
                        {elseif !empty($s.id_utente_segnalato)}
                            Utente: {if !empty($s.utente_segnalato_username)}{$s.utente_segnalato_username}{else}#{$s.id_utente_segnalato}{/if}
                        {elseif !empty($s.id_business)}
                            Business: {if !empty($s.business_nome)}{$s.business_nome}{else}#{$s.id_business}{/if}
                        {elseif !empty($s.id_feedback)}
                            Feedback #{$s.id_feedback}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>{$s.stato|default:''}</td>
                    <td>{$s.data_segnalazione|default:''}</td>
                    <td>
                        {if ($s.stato|default:'') != 'Risolta'}
                            <form class="u-post-form-flex" method="post" action="/segnalazione/close">
                                <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                                <input type="hidden" name="id_segnalazione" value="{$s.id_segnalazione|default:0}">
                                <button class="btn" type="submit">Chiudi</button>
                            </form>
                        {/if}
                        <form class="u-post-form-flex" method="post" action="/segnalazione/delete">
                            <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
                            <input type="hidden" name="id_segnalazione" value="{$s.id_segnalazione|default:0}">
                            <button class="btn btn-danger" type="submit">Elimina</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p>Nessuna segnalazione presente.</p>
{/if}

{include file="layouts/footer.tpl"}
