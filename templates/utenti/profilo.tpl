{include file="layouts/header.tpl"}

{if !empty($utente)}
    {assign var=displayName value=$utente.username|default:'Utente'}
    {assign var=editing value=$editingIndirizzo|default:[]}
    {assign var=indirizziCount value=$indirizziUtente|count_items}
    {assign var=annunciCount value=$annunciUtente|count_items}
    {assign var=pagamentiCount value=$cronologiaPagamenti|count_items}
    {assign var=indirizzoPrincipale value=[]}
    {if !empty($indirizziUtente)}
        {assign var=indirizzoPrincipale value=$indirizziUtente.0}
        {foreach $indirizziUtente as $indirizzo}
            {if !empty($indirizzo.predefinito)}
                {assign var=indirizzoPrincipale value=$indirizzo}
            {/if}
        {/foreach}
    {/if}

    <div class="nv-profile">
        {if !empty($errore)}
            <div class="alert alert-error">{$errore}</div>
        {/if}
        {if ($get.profilo_aggiornato|default:'') == '1'}
            <div class="alert alert-success">Profilo aggiornato.</div>
        {/if}
        {if ($get.password_aggiornata|default:'') == '1'}
            <div class="alert alert-success">Password aggiornata.</div>
        {/if}

        <section class="nv-profile-hero" aria-label="Riepilogo profilo">
            <div class="nv-profile-avatar-block">
                <form class="nv-profile-avatar-form" method="post" action="index.php" enctype="multipart/form-data">
                    <input type="hidden" name="route" value="profilo-propic-store">
                    <label class="nv-profile-avatar" for="profileAvatarUpload" aria-label="Cambia foto profilo">
                        {if !empty($utente.propic)}
                            <img src="{$utente.propic}" alt="Foto profilo">
                        {else}
                            <span>{$displayName|substr:0:1|strtoupper}</span>
                        {/if}
                    </label>
                    <input class="nv-profile-avatar-input" id="profileAvatarUpload" type="file" name="propic" accept="image/jpeg,image/png,image/webp" onchange="this.form.submit()">
                </form>
                <p>Click per aggiornare</p>
            </div>

            <div class="nv-profile-main">
                <span class="nv-profile-badge">{if $isBusiness}Account business{else}Account personale{/if}</span>
                <h1>{$displayName}</h1>
                <p class="nv-profile-copy">{if $isBusiness}Gestisci profilo, annunci e vendite da un unico pannello.{else}Gestisci profilo, spedizioni, annunci e acquisti da un unico pannello.{/if}</p>

                <div class="nv-profile-info-grid">
                    <div class="nv-profile-info-item">
                        <span>Email</span>
                        <strong>{$utente.email|default:'Non indicata'}</strong>
                    </div>
                    <div class="nv-profile-info-item">
                        <span>Telefono</span>
                        <strong>{$utente.telefono|default:'Non indicato'}</strong>
                    </div>
                    <div class="nv-profile-info-item nv-profile-info-wide">
                        <span>Indirizzo principale</span>
                        {if !empty($indirizzoPrincipale)}
                            <strong>{$indirizzoPrincipale.via|default:''} {$indirizzoPrincipale.numero|default:''}<br>{$indirizzoPrincipale.citta|default:''}</strong>
                        {else}
                            <strong>Non completato</strong>
                        {/if}
                    </div>
                </div>

                <div class="nv-profile-inline-actions">
                    <details class="nv-profile-action-details">
                        <summary class="nv-profile-small-btn">Modifica dati</summary>
                        <form class="nv-profile-popover-form" method="post" action="index.php">
                            <input type="hidden" name="route" value="profilo-update">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" value="{$utente.nome|default:''}" required>
                            <label for="telefono">Telefono</label>
                            <input type="text" id="telefono" name="telefono" value="{$utente.telefono|default:''}">
                            <button class="btn" type="submit">Salva dati</button>
                        </form>
                    </details>

                    <details class="nv-profile-action-details">
                        <summary class="nv-profile-small-btn">Cambia password</summary>
                        <form class="nv-profile-popover-form" method="post" action="index.php">
                            <input type="hidden" name="route" value="profilo-password">
                            <label for="password_attuale">Password attuale</label>
                            <input type="password" id="password_attuale" name="password_attuale" required>
                            <label for="nuova_password">Nuova password</label>
                            <input type="password" id="nuova_password" name="nuova_password" required>
                            <label for="password_confirm">Conferma nuova password</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                            <button class="btn" type="submit">Aggiorna password</button>
                        </form>
                    </details>

                    {if !$isBusiness}
                        <details class="nv-profile-action-details nv-profile-address-details" {if !empty($editing)}open{/if}>
                            <summary class="nv-profile-small-btn">{if !empty($editing)}Modifica indirizzo{else}Aggiungi indirizzo di spedizione{/if}</summary>
                            <form class="nv-profile-popover-form nv-profile-address-popover" method="post" action="index.php">
                                <input type="hidden" name="route" value="{if !empty($editing)}profilo-indirizzo-update{else}profilo-indirizzo-store{/if}">
                                {if !empty($editing)}
                                    <input type="hidden" name="id_indirizzo" value="{$editing.id_indirizzo|default:0}">
                                {/if}

                                <div class="nv-field nv-field-wide">
                                    <label for="indirizzo_nome">Nome e cognome</label>
                                    <input type="text" id="indirizzo_nome" name="nome" value="{$utente.nome|default:''}" placeholder="Nome destinatario">
                                </div>
                                <div class="nv-field nv-field-wide">
                                    <label for="via">Via / corso / piazza</label>
                                    <input type="text" id="via" name="via" value="{$editing.via|default:''}" required>
                                </div>
                                <div class="nv-field">
                                    <label for="numero">Numero civico</label>
                                    <input type="text" id="numero" name="numero" value="{$editing.numero|default:''}">
                                </div>
                                <div class="nv-field">
                                    <label for="cap">CAP</label>
                                    <input type="text" id="cap" name="cap" value="{$editing.cap|default:''}">
                                </div>
                                <div class="nv-field">
                                    <label for="citta">Citta</label>
                                    <input type="text" id="citta" name="citta" value="{$editing.citta|default:''}" required>
                                </div>
                                <div class="nv-field">
                                    <label for="provincia">Provincia</label>
                                    <input type="text" id="provincia" name="provincia" value="{$editing.provincia|default:''}">
                                </div>
                                <div class="nv-field nv-field-wide">
                                    <label for="paese">Paese</label>
                                    <input type="text" id="paese" name="paese" value="{$editing.paese|default:'Italia'}">
                                </div>

                                <div class="nv-address-actions">
                                    <button class="btn" type="submit">{if !empty($editing)}Salva modifiche{else}Salva nuovo indirizzo{/if}</button>
                                    {if !empty($editing)}
                                        <a class="btn btn-secondary" href="index.php?route=profilo">Annulla</a>
                                    {/if}
                                </div>
                            </form>
                        </details>
                    {/if}
                </div>
            </div>

            <div class="nv-profile-stats">
                <div class="nv-profile-stat">
                    <strong>{$annunciCount}</strong>
                    <span>Annunci attivi</span>
                </div>
                <div class="nv-profile-stat">
                    <strong>{$pagamentiCount}</strong>
                    <span>{if $isBusiness}Vendite concluse{else}Acquisti transitati{/if}</span>
                </div>
            </div>
        </section>

        <nav class="nv-profile-quick-actions" aria-label="Azioni profilo">
            <a class="nv-profile-chip nv-profile-chip-gold" href="index.php?route=annuncio-create">Crea annuncio</a>
            <a class="nv-profile-chip" href="index.php?route=feedback">I miei feedback</a>
        </nav>

        {if !$isBusiness}
            <section class="nv-profile-section">
                <div class="nv-profile-section-title">
                    <h2>Tutti gli indirizzi</h2>
                    <p>Gestisci gli indirizzi disponibili durante il tuo profilo.</p>
                </div>

                {if !empty($indirizziUtente)}
                    <div class="nv-address-list">
                        {foreach $indirizziUtente as $indirizzo}
                            <article class="nv-address-card">
                                <p>
                                    {$indirizzo.via|default:''} {$indirizzo.numero|default:''}, {$indirizzo.cap|default:''} {$indirizzo.citta|default:''}{if !empty($indirizzo.provincia)} ({$indirizzo.provincia}){/if}, {$indirizzo.paese|default:'Italia'}
                                    {if !empty($indirizzo.predefinito)}<span class="seller-pro-badge">Predefinito</span>{/if}
                                </p>
                                <div>
                                    <a class="btn btn-secondary" href="index.php?route=profilo-indirizzo-edit&id={$indirizzo.id_indirizzo|default:0}">Modifica</a>
                                    {if empty($indirizzo.predefinito)}
                                        <a class="btn btn-secondary" href="index.php?route=profilo-indirizzo-default&id={$indirizzo.id_indirizzo|default:0}">Predefinito</a>
                                    {/if}
                                    <a class="btn btn-danger" href="index.php?route=profilo-indirizzo-delete&id={$indirizzo.id_indirizzo|default:0}">Elimina</a>
                                </div>
                            </article>
                        {/foreach}
                    </div>
                {else}
                    <div class="nv-empty-box">Non hai ancora indirizzi salvati.</div>
                {/if}
            </section>
        {/if}

        <section class="nv-profile-section">
            <div class="nv-profile-section-title nv-profile-title-row">
                <div>
                    <h2>{$titoloAnnunciProfilo|default:'Annunci attivi'}</h2>
                    <p>Controlla gli oggetti pubblicati e passa rapidamente tra attivi e venduti.</p>
                </div>
                <div class="nv-profile-tabs" aria-label="Mostra annunci">
                    <span>Mostra annunci</span>
                    <a class="{if ($filtroAnnunci|default:'attivo') == 'attivo'}is-active{/if}" href="index.php?route=profilo-annunci-attivi">Annunci attivi</a>
                    <a class="{if ($filtroAnnunci|default:'attivo') == 'venduto'}is-active{/if}" href="index.php?route=profilo-annunci-venduti">Annunci venduti</a>
                </div>
            </div>

            {if !empty($annunciUtente)}
                <div class="grid profile-grid nv-profile-annunci-grid">
                    {foreach $annunciUtente as $annuncio}
                        {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                    {/foreach}
                </div>
            {else}
                <div class="nv-empty-box">{if ($filtroAnnunci|default:'attivo') == 'venduto'}Non hai annunci venduti.{else}Non hai annunci attivi.{/if}</div>
            {/if}
        </section>

        {if !$isBusiness}
            <section class="nv-profile-section">
                <div class="nv-profile-section-title">
                    <h2>Cronologia pagamenti</h2>
                    <p>Rivedi gli acquisti conclusi e lascia feedback quando disponibile.</p>
                </div>

                {if !empty($cronologiaPagamenti)}
                    <div class="nv-table-wrap">
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
                    </div>
                {else}
                    <div class="nv-empty-box">Nessun acquisto effettuato.</div>
                {/if}
            </section>
        {/if}
    </div>

{else}
    <div class="alert alert-error">Profilo non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
