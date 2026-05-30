{* Profilo utente personale: interfaccia allineata a NerdVault Pages.html, mantenendo form e logica Smarty esistenti. *}
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

    <nav class="pg-breadcrumb">
        <a href="/home/index">Home</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="current">Il mio profilo</span>
    </nav>

    {if !empty($errore)}
        <div class="pg-alert" data-tone="danger">{$errore}</div>
    {/if}
    {if ($get.profilo_aggiornato|default:'') == '1'}
        <div class="pg-alert" data-tone="success">Profilo aggiornato.</div>
    {/if}
    {if ($get.password_aggiornata|default:'') == '1'}
        <div class="pg-alert" data-tone="success">Password aggiornata.</div>
    {/if}

    <section class="pf-hero" aria-label="Riepilogo profilo">
        <div class="pf-hero-left">
            <form class="pf-avatar-form" method="post" action="/utente/propic-store" enctype="multipart/form-data">
                <label class="pf-avatar" for="profileAvatarUpload" aria-label="Cambia foto profilo">
                    {if !empty($utente.propic)}
                        <img src="{$utente.propic}" alt="Foto profilo">
                    {else}
                        <span>{$displayName|substr:0:1|strtoupper}</span>
                    {/if}
                    <span class="pf-avatar-edit" aria-hidden="true">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v4"></path><path d="M12 17v4"></path><path d="M3 12h4"></path><path d="M17 12h4"></path></svg>
                    </span>
                </label>
                <input id="profileAvatarUpload" type="file" name="propic" accept="image/jpeg,image/png,image/webp" onchange="this.form.submit()">
            </form>

            <div class="pf-id">
                <span class="pg-pill">{if $isBusiness}Account business{else}Account personale{/if}</span>
                <h1 class="pf-name">{$displayName}</h1>
                <p class="pg-sub">{if !empty($utente.nome)}{$utente.nome}{else}@{$displayName}{/if} &middot; {if $isBusiness}Area venditore{else}Membro NerdVault{/if}</p>

                <div class="pf-info">
                    <div>
                        <span>Email</span>
                        <strong>{$utente.email|default:'Non indicata'}</strong>
                    </div>
                    <div>
                        <span>Telefono</span>
                        <strong>{$utente.telefono|default:'Non indicato'}</strong>
                    </div>
                    <div>
                        <span>Indirizzo principale</span>
                        {if !empty($indirizzoPrincipale)}
                            <strong>{$indirizzoPrincipale.via|default:''} {$indirizzoPrincipale.numero|default:''}, {$indirizzoPrincipale.citta|default:''}</strong>
                        {else}
                            <strong>Non completato</strong>
                        {/if}
                    </div>
                </div>

                <div class="pf-actions">
                    <details class="pf-details">
                        <summary class="btn" data-variant="dark" data-size="sm"><span aria-hidden="true">&#9998;</span> Modifica dati</summary>
                        <form class="pf-popover" method="post" action="/utente/update">
                            <div class="pg-field">
                                <label class="pg-label" for="nome">Nome</label>
                                <input class="pg-input" type="text" id="nome" name="nome" value="{$utente.nome|default:''}" required>
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="telefono">Telefono</label>
                                <input class="pg-input" type="text" id="telefono" name="telefono" value="{$utente.telefono|default:''}">
                            </div>
                            <button class="btn" type="submit">Salva dati</button>
                        </form>
                    </details>

                    <details class="pf-details">
                        <summary class="btn" data-variant="dark" data-size="sm">Cambia password <span aria-hidden="true">&#128273;</span></summary>
                        <form class="pf-popover" method="post" action="/utente/password">
                            <div class="pg-field">
                                <label class="pg-label" for="password_attuale">Password attuale</label>
                                <input class="pg-input" type="password" id="password_attuale" name="password_attuale" required>
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="nuova_password">Nuova password</label>
                                <input class="pg-input" type="password" id="nuova_password" name="nuova_password" required>
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="password_confirm">Conferma nuova password</label>
                                <input class="pg-input" type="password" id="password_confirm" name="password_confirm" required>
                            </div>
                            <button class="btn" type="submit">Aggiorna password</button>
                        </form>
                    </details>
                </div>
            </div>
        </div>

        <div class="pf-stats">
            <div class="pf-stat"><strong>{$annunciCount}</strong><span>Annunci attivi</span></div>
            <div class="pf-stat"><strong>{$pagamentiCount}</strong><span>{if $isBusiness}Vendite{else}Acquisti{/if}</span></div>
            <div class="pf-stat"><strong class="pf-stat-gold">{$indirizziCount}</strong><span>Indirizzi</span></div>
            <a class="btn btn-gold" data-size="sm" href="/annuncio/create">Crea annuncio</a>
        </div>
    </section>

    {if !$isBusiness}
        <section class="pf-section">
            <div class="pf-section-head">
                <div>
                    <h2 class="pf-section-title">Indirizzi di spedizione</h2>
                    <p class="pg-sub">Gestisci dove ricevere i tuoi ordini.</p>
                </div>
                <details class="pf-details pf-add-details">
                    <summary class="btn" data-variant="ghost" data-size="sm">+ Aggiungi</summary>
                    <form class="pf-popover pf-popover-right" method="post" action="/utente/indirizzo-store">
                        <div class="pg-field">
                            <label class="pg-label" for="add_nome">Nome e cognome</label>
                            <input class="pg-input" type="text" id="add_nome" name="nome" value="{$utente.nome|default:''}" placeholder="Nome destinatario">
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="add_via">Via / corso / piazza</label>
                            <input class="pg-input" type="text" id="add_via" name="via" value="" required>
                        </div>
                        <div class="pg-field-row">
                            <div>
                                <label class="pg-label" for="add_numero">Numero civico</label>
                                <input class="pg-input" type="text" id="add_numero" name="numero" value="">
                            </div>
                            <div>
                                <label class="pg-label" for="add_cap">CAP</label>
                                <input class="pg-input" type="text" id="add_cap" name="cap" value="">
                            </div>
                        </div>
                        <div class="pg-field-row">
                            <div>
                                <label class="pg-label" for="add_citta">Citta</label>
                                <input class="pg-input" type="text" id="add_citta" name="citta" value="" required>
                            </div>
                            <div>
                                <label class="pg-label" for="add_provincia">Provincia</label>
                                <input class="pg-input" type="text" id="add_provincia" name="provincia" value="">
                            </div>
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="add_paese">Paese</label>
                            <input class="pg-input" type="text" id="add_paese" name="paese" value="Italia">
                        </div>
                        <button class="btn" type="submit">Salva nuovo indirizzo</button>
                    </form>
                </details>
            </div>

            {if !empty($indirizziUtente)}
                <div class="pf-addr-grid">
                    {foreach $indirizziUtente as $indirizzo}
                        <article class="pf-addr">
                            {if !empty($indirizzo.predefinito)}<span class="pg-pill" data-tone="success">Predefinito</span>{/if}
                            <p class="pf-addr-text">
                                {$indirizzo.via|default:''} {$indirizzo.numero|default:''}<br>
                                {$indirizzo.cap|default:''} {$indirizzo.citta|default:''}{if !empty($indirizzo.provincia)} ({$indirizzo.provincia}){/if}<br>
                                {$indirizzo.paese|default:'Italia'}
                            </p>
                            <div class="pf-addr-actions">
                                <a class="va-link" href="/utente/indirizzo-edit/{$indirizzo.id_indirizzo|default:0}">Modifica</a>
                                {if empty($indirizzo.predefinito)}
                                    <a class="va-link" href="/utente/indirizzo-default/{$indirizzo.id_indirizzo|default:0}">Predefinito</a>
                                {/if}
                                <a class="va-link pf-link-danger" href="/utente/indirizzo-delete/{$indirizzo.id_indirizzo|default:0}">Elimina</a>
                            </div>
                        </article>
                    {/foreach}
                </div>
            {else}
                <div class="pg-card"><p class="muted" style="margin:0;">Non hai ancora indirizzi salvati.</p></div>
            {/if}

            {if !empty($editing)}
                <details class="pf-details pf-edit-address" open>
                    <summary class="btn" data-variant="dark" data-size="sm">Modifica indirizzo selezionato</summary>
                    <form class="pf-popover" method="post" action="/utente/indirizzo-update">
                        <input type="hidden" name="id_indirizzo" value="{$editing.id_indirizzo|default:0}">
                        <div class="pg-field">
                            <label class="pg-label" for="edit_nome">Nome e cognome</label>
                            <input class="pg-input" type="text" id="edit_nome" name="nome" value="{$utente.nome|default:''}" placeholder="Nome destinatario">
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="edit_via">Via / corso / piazza</label>
                            <input class="pg-input" type="text" id="edit_via" name="via" value="{$editing.via|default:''}" required>
                        </div>
                        <div class="pg-field-row">
                            <div>
                                <label class="pg-label" for="edit_numero">Numero civico</label>
                                <input class="pg-input" type="text" id="edit_numero" name="numero" value="{$editing.numero|default:''}">
                            </div>
                            <div>
                                <label class="pg-label" for="edit_cap">CAP</label>
                                <input class="pg-input" type="text" id="edit_cap" name="cap" value="{$editing.cap|default:''}">
                            </div>
                        </div>
                        <div class="pg-field-row">
                            <div>
                                <label class="pg-label" for="edit_citta">Citta</label>
                                <input class="pg-input" type="text" id="edit_citta" name="citta" value="{$editing.citta|default:''}" required>
                            </div>
                            <div>
                                <label class="pg-label" for="edit_provincia">Provincia</label>
                                <input class="pg-input" type="text" id="edit_provincia" name="provincia" value="{$editing.provincia|default:''}">
                            </div>
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="edit_paese">Paese</label>
                            <input class="pg-input" type="text" id="edit_paese" name="paese" value="{$editing.paese|default:'Italia'}">
                        </div>
                        <div class="pg-actions">
                            <button class="btn" type="submit">Salva modifiche</button>
                            <a class="btn btn-secondary" href="/utente/profilo">Annulla</a>
                        </div>
                    </form>
                </details>
            {/if}
        </section>
    {/if}

    <section class="pf-section">
        <div class="pf-section-head">
            <div>
                <h2 class="pf-section-title">I miei annunci</h2>
                <p class="pg-sub">Passa tra attivi e venduti.</p>
            </div>
            <div class="pg-tabs pf-tabs" aria-label="Mostra annunci">
                <a class="pg-tab {if ($filtroAnnunci|default:'attivo') == 'attivo'}is-active{/if}" href="/utente/profilo">Attivi <span class="pg-tab-count">{$annunciCount}</span></a>
                <a class="pg-tab {if ($filtroAnnunci|default:'attivo') == 'venduto'}is-active{/if}" href="/utente/profilo-venduti">Venduti</a>
            </div>
        </div>

        {if !empty($annunciUtente)}
            <div class="grid pf-grid">
                {foreach $annunciUtente as $annuncio}
                    {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                {/foreach}
            </div>
        {else}
            <div class="pg-card"><p class="muted" style="margin:0;">{if ($filtroAnnunci|default:'attivo') == 'venduto'}Non hai annunci venduti.{else}Non hai annunci attivi.{/if}</p></div>
        {/if}
    </section>

    {if !$isBusiness}
        <section class="pf-section">
            <div class="pf-section-head">
                <div>
                    <h2 class="pf-section-title">Cronologia acquisti</h2>
                    <p class="pg-sub">Rivedi gli ordini e lascia un feedback.</p>
                </div>
            </div>

            {if !empty($cronologiaPagamenti)}
                <div class="pf-table">
                    <div class="pf-tr pf-th">
                        <span>Ordine</span><span>Articolo</span><span>Importo</span><span>Stato</span><span>Data</span><span></span>
                    </div>
                    {foreach $cronologiaPagamenti as $pagamento}
                        <div class="pf-tr">
                            <span class="pf-mono">#{$pagamento.id_pagamento|default:''}</span>
                            <span class="pf-ellip">{$pagamento.titolo|default:''}</span>
                            <span class="pf-mono pf-amount">&euro; {$pagamento.importo_totale|default:0|number_format:2:",":"."}</span>
                            <span><span class="pg-pill" data-tone="{if ($pagamento.stato|default:'') == 'completato'}success{else}gold{/if}">{$pagamento.stato|default:''}</span></span>
                            <span class="pg-sub">{$pagamento.data|default:$pagamento.data_pagamento|default:''}</span>
                            <span><a class="btn" data-variant="ghost" data-size="sm" href="/feedback/create/{$pagamento.id_pagamento|default:0}">Feedback</a></span>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="pg-card"><p class="muted" style="margin:0;">Nessun acquisto effettuato.</p></div>
            {/if}
        </section>
    {/if}
{else}
    <div class="pg-alert" data-tone="danger">Profilo non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
