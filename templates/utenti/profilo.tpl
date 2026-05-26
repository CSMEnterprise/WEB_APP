{include file="layouts/header.tpl"}

{if !empty($utente)}
    {assign var=displayName value=$utente.username|default:'Utente'}
    {assign var=editing value=$editingIndirizzo|default:[]}

    <div class="profile-page">
        {if !empty($errore)}
            <div class="alert alert-error">{$errore}</div>
        {/if}
        {if ($get.profilo_aggiornato|default:'') == '1'}
            <div class="alert alert-success">Profilo aggiornato.</div>
        {/if}
        {if ($get.password_aggiornata|default:'') == '1'}
            <div class="alert alert-success">Password aggiornata.</div>
        {/if}

        <section class="profile-hero" aria-label="Riepilogo profilo">
            <form method="post" action="index.php" enctype="multipart/form-data" id="propic-form" class="profile-avatar-form">
                <input type="hidden" name="route" value="profilo-propic-store">
                <input type="file" id="propic-input" name="propic" accept="image/jpeg,image/png,image/webp" required hidden>
                <button type="button" id="propic-button" class="profile-avatar-button" aria-label="Cambia foto profilo" title="Clicca per cambiare foto profilo">
                    {if !empty($utente.propic)}
                        <img src="{$utente.propic}" alt="Foto profilo">
                    {else}
                        <span class="profile-avatar-initial">{$displayName|substr:0:1|strtoupper}</span>
                    {/if}
                </button>
                <p class="profile-avatar-hint">Clicca per aggiornare</p>
            </form>

            <div class="profile-summary">
                <span class="profile-kicker">{if $isBusiness}Account business{else}Account personale{/if}</span>
                <h1>{$displayName}</h1>
                <p class="profile-summary-copy">Gestisci profilo, annunci, indirizzi e cronologia.</p>

                <div class="profile-info-grid">
                    <div class="profile-info-item"><span>Email</span><strong>{$utente.email|default:'Non indicata'}</strong></div>
                    <div class="profile-info-item"><span>Nome</span><strong>{$utente.nome|default:'Non indicato'}</strong></div>
                    <div class="profile-info-item"><span>Telefono</span><strong>{$utente.telefono|default:'Non indicato'}</strong></div>
                    <div class="profile-info-item"><span>Registrato dal</span><strong>{if !empty($utente.data_registrazione)}{$utente.data_registrazione|date_it}{else}Non indicato{/if}</strong></div>
                </div>
            </div>

            <div class="profile-stats" aria-label="Statistiche profilo">
                <div class="profile-stat"><span class="profile-stat-value">{$annunciUtente|count_items}</span><span class="profile-stat-label">Annunci</span></div>
                <div class="profile-stat"><span class="profile-stat-value">{$cronologiaPagamenti|count_items}</span><span class="profile-stat-label">Acquisti</span></div>
            </div>
        </section>

        <section class="grid-2">
            <article class="card">
                <h2>Dati profilo</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="route" value="profilo-update">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="{$utente.nome|default:''}" required>
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" value="{$utente.telefono|default:''}">
                    <button class="btn" type="submit">Salva profilo</button>
                </form>
            </article>

            <article class="card">
                <h2>Password</h2>
                <form method="post" action="index.php">
                    <input type="hidden" name="route" value="profilo-password">
                    <label for="password_attuale">Password attuale</label>
                    <input type="password" id="password_attuale" name="password_attuale" required>
                    <label for="nuova_password">Nuova password</label>
                    <input type="password" id="nuova_password" name="nuova_password" required>
                    <label for="password_confirm">Conferma nuova password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                    <button class="btn" type="submit">Aggiorna password</button>
                </form>
            </article>
        </section>

        {if !$isBusiness}
            <section class="profile-section">
                <div class="profile-section-header">
                    <div>
                        <h2>Indirizzi di spedizione</h2>
                        <p>Usali per completare gli acquisti.</p>
                    </div>
                </div>

                <div class="grid-2">
                    <article class="card">
                        <h3>{if !empty($editing)}Modifica indirizzo{else}Nuovo indirizzo{/if}</h3>
                        <form method="post" action="index.php">
                            <input type="hidden" name="route" value="{if !empty($editing)}profilo-indirizzo-update{else}profilo-indirizzo-store{/if}">
                            {if !empty($editing)}
                                <input type="hidden" name="id_indirizzo" value="{$editing.id_indirizzo|default:0}">
                            {/if}
                            <label for="indirizzo_nome">Nome destinatario</label>
                            <input type="text" id="indirizzo_nome" name="nome" value="{$utente.nome|default:''}">
                            <label for="via">Via</label>
                            <input type="text" id="via" name="via" value="{$editing.via|default:''}" required>
                            <label for="numero">Numero</label>
                            <input type="text" id="numero" name="numero" value="{$editing.numero|default:''}">
                            <label for="cap">CAP</label>
                            <input type="text" id="cap" name="cap" value="{$editing.cap|default:''}">
                            <label for="citta">Citta</label>
                            <input type="text" id="citta" name="citta" value="{$editing.citta|default:''}" required>
                            <label for="provincia">Provincia</label>
                            <input type="text" id="provincia" name="provincia" value="{$editing.provincia|default:''}">
                            <label for="paese">Paese</label>
                            <input type="text" id="paese" name="paese" value="{$editing.paese|default:'Italia'}">
                            <button class="btn" type="submit">{if !empty($editing)}Salva modifiche{else}Aggiungi indirizzo{/if}</button>
                            {if !empty($editing)}
                                <a class="btn btn-secondary" href="index.php?route=profilo">Annulla</a>
                            {/if}
                        </form>
                    </article>

                    <article class="card">
                        <h3>Indirizzi salvati</h3>
                        {if !empty($indirizziUtente)}
                            {foreach $indirizziUtente as $indirizzo}
                                <div class="profile-address-item">
                                    <p>
                                        {$indirizzo.via|default:''} {$indirizzo.numero|default:''}, {$indirizzo.cap|default:''} {$indirizzo.citta|default:''}{if !empty($indirizzo.provincia)} ({$indirizzo.provincia}){/if}, {$indirizzo.paese|default:'Italia'}
                                        {if !empty($indirizzo.predefinito)}<span class="seller-pro-badge">Predefinito</span>{/if}
                                    </p>
                                    <a class="btn btn-secondary" href="index.php?route=profilo-indirizzo-edit&id={$indirizzo.id_indirizzo|default:0}">Modifica</a>
                                    {if empty($indirizzo.predefinito)}
                                        <a class="btn btn-secondary" href="index.php?route=profilo-indirizzo-default&id={$indirizzo.id_indirizzo|default:0}">Predefinito</a>
                                    {/if}
                                    <a class="btn btn-danger" href="index.php?route=profilo-indirizzo-delete&id={$indirizzo.id_indirizzo|default:0}">Elimina</a>
                                </div>
                            {/foreach}
                        {else}
                            <p class="muted">Nessun indirizzo salvato.</p>
                        {/if}
                    </article>
                </div>
            </section>
        {/if}

        <section class="profile-annunci">
            <div class="profile-section-header">
                <div>
                    <h2>{$titoloAnnunciProfilo|default:'Annunci'}</h2>
                    <p>Gestisci gli oggetti pubblicati.</p>
                </div>
                <div>
                    <a class="btn btn-secondary" href="index.php?route=profilo-annunci-attivi">Attivi</a>
                    <a class="btn btn-secondary" href="index.php?route=profilo-annunci-venduti">Venduti</a>
                    <a class="btn" href="index.php?route=annuncio-create">Nuovo annuncio</a>
                </div>
            </div>

            {if !empty($annunciUtente)}
                <div class="grid profile-grid">
                    {foreach $annunciUtente as $annuncio}
                        {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                    {/foreach}
                </div>
            {else}
                <div class="card profile-empty"><p>Nessun annuncio in questa sezione.</p></div>
            {/if}
        </section>

        {if !$isBusiness}
            <section class="profile-section">
                <h2>Cronologia pagamenti</h2>
                {if !empty($cronologiaPagamenti)}
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Annuncio</th><th>Importo</th><th>Stato</th><th>Data</th><th>Feedback</th></tr>
                        </thead>
                        <tbody>
                            {foreach $cronologiaPagamenti as $pagamento}
                                <tr>
                                    <td>{$pagamento.id_pagamento|default:''}</td>
                                    <td>{$pagamento.titolo|default:''}</td>
                                    <td>&euro; {$pagamento.importo_totale|default:0|number_format:2:",":"."}</td>
                                    <td>{$pagamento.stato|default:''}</td>
                                    <td>{$pagamento.data|default:$pagamento.data_pagamento|default:''}</td>
                                    <td><a class="btn btn-secondary" href="index.php?route=feedback-create&id_pagamento={$pagamento.id_pagamento|default:0}">Feedback</a></td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    <div class="card"><p>Nessun pagamento registrato.</p></div>
                {/if}
            </section>
        {/if}
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const propicInput = document.getElementById('propic-input');
        const propicButton = document.getElementById('propic-button');
        const propicForm = document.getElementById('propic-form');

        if (propicButton && propicInput) {
            propicButton.addEventListener('click', function () {
                propicInput.click();
            });
        }

        if (propicInput && propicForm) {
            propicInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    propicForm.submit();
                }
            });
        }
    });
    </script>
{else}
    <div class="alert alert-error">Profilo non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
