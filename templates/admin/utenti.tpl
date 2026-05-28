{* Gestione utenti admin: ricerca e ban/sblocco utenti registrati. Gli admin di livello 2 vedono anche la sezione di gestione degli altri admin. *}
{include file="layouts/header.tpl"}

<h1>Gestione utenti</h1>

{* La sezione gestione admin è visibile solo al super-admin (livello 2) *}
{if $livelloSicurezza == 2}
    <section class="u-style-002">
        <h2>Gestione admin</h2>
        {if !empty($admins)}
            <table>
                <thead>
                    <tr><th>ID</th><th>Email</th><th>Livello</th><th>Stato</th><th>Data creazione</th><th>Azione</th></tr>
                </thead>
                <tbody>
                    {foreach $admins as $admin}
                        <tr>
                            <td>{$admin.id_admin|default:''}</td>
                            <td>{$admin.email|default:''}</td>
                            <td>{$admin.livello_sicurezza|default:''}</td>
                            <td>{if !empty($admin.stato_ban)}Bannato{else}Attivo{/if}</td>
                            <td>{$admin.data_creazione|default:''}</td>
                            <td>
                                {if ($admin.id_admin|default:0) == $userId}
                                    <span class="muted">Account corrente</span>
                                {elseif ($admin.livello_sicurezza|default:1) != 1}
                                    <span class="muted">Non moderabile</span>
                                {elseif !empty($admin.stato_ban)}
                                    <a class="btn" href="/admin/sblocca-admin/{$admin.id_admin|default:0}">Sblocca</a>
                                {else}
                                    <a class="btn btn-danger" href="/admin/banna-admin/{$admin.id_admin|default:0}">Banna</a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <p>Nessun admin trovato.</p>
        {/if}
    </section>
{/if}

<h2>Utenti registrati</h2>

<section class="card">
    <h3>Ricerca utenti registrati</h3>
    <form method="get" action="/admin/utenti">
                <label for="q_utente">Utente</label>
        <input type="search" id="q_utente" name="q_utente" placeholder="Cerca per ID, username, email, nome o telefono" value="{$filters.q_utente|default:''}">
        <button class="btn" type="submit">Cerca</button>
        <a class="btn btn-secondary" href="/admin/utenti">Reset</a>
    </form>
</section>

{if !empty($utenti)}
    <table>
        <thead>
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Stato</th><th>Azione</th></tr>
        </thead>
        <tbody>
            {foreach $utenti as $utente}
                <tr>
                    <td>{$utente.id_utente|default:''}</td>
                    <td>{$utente.username|default:''}</td>
                    <td>{$utente.email|default:''}</td>
                    <td>{if !empty($utente.stato_ban)}Bannato{else}Attivo{/if}</td>
                    <td>
                        {if !empty($utente.stato_ban)}
                            <a class="btn" href="/admin/sblocca-utente/{$utente.id_utente|default:0}">Sblocca</a>
                        {else}
                            <a class="btn btn-danger" href="/admin/banna-utente/{$utente.id_utente|default:0}">Banna</a>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p>Nessun utente trovato.</p>
{/if}

{include file="layouts/footer.tpl"}
