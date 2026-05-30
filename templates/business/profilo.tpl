{include file="layouts/header.tpl"}

<nav class="pg-breadcrumb" aria-label="Percorso">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span class="current">Vetrina</span>
</nav>

{if !empty($errore)}
    <div class="pg-alert" data-tone="danger">{$errore}</div>
{/if}

{if !empty($business)}
    {assign var=businessName value=$business.nome_azienda|default:'NerdVault Store'}
    {assign var=isPublicVetrina value=$isPublicVetrina|default:false}
    {assign var=annunciTotali value=$annunci|default:[]|count}
    {assign var=annunciAttivi value=0}
    {assign var=annunciVenduti value=0}
    {assign var=prezzoMedio value=0}
    {assign var=prezzoTotale value=0}
    {foreach $annunci|default:[] as $statAnnuncio}
        {assign var=statState value=$statAnnuncio.stato|default:'attivo'|lower}
        {if $statState == 'attivo'}{assign var=annunciAttivi value=$annunciAttivi+1}{/if}
        {if $statState == 'venduto'}{assign var=annunciVenduti value=$annunciVenduti+1}{/if}
        {assign var=prezzoTotale value=$prezzoTotale+($statAnnuncio.prezzo|default:0)}
    {/foreach}
    {if $annunciTotali > 0}{assign var=prezzoMedio value=$prezzoTotale/$annunciTotali}{/if}

    <section class="bs-banner" aria-label="Vetrina venditore">
        <div class="bs-cover"></div>
        <div class="bs-header">
            <div class="bs-avatar" aria-hidden="true">{$businessName|truncate:1:"":true|upper}</div>
            <div class="bs-headinfo">
                <div class="bs-headtop">
                    <h1>{$businessName}</h1>
                    <span class="nv-pro-badge">PRO</span>
                </div>
                <p class="bs-handle">{$business.email_aziendale|default:'vetrina@nerdvault.local'}</p>
                <div class="bs-rating" aria-label="Valutazione venditore">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"></path></svg>
                    4.9 <span>Feedback verificati</span>
                </div>
            </div>
            <div class="bs-actions">
                {if $isPublicVetrina}
                    <a class="btn" href="#vetrina-annunci">Vedi annunci</a>
                {else}
                    <a class="btn" href="/annuncio/create">Crea annuncio</a>
                    <a class="btn btn-secondary" href="/business/ordini">Ordini ricevuti</a>
                {/if}
            </div>
        </div>
    </section>

    <section class="bs-stats" aria-label="Riepilogo vetrina">
        <div class="bs-stat">
            <span class="bs-stat-label">Annunci</span>
            <strong>{$annunciTotali}</strong>
        </div>
        <div class="bs-stat">
            <span class="bs-stat-label">Attivi</span>
            <strong>{$annunciAttivi}</strong>
        </div>
        <div class="bs-stat">
            <span class="bs-stat-label">Venduti</span>
            <strong>{$annunciVenduti}</strong>
        </div>
        <div class="bs-stat">
            <span class="bs-stat-label">Prezzo medio</span>
            <strong>&euro; {$prezzoMedio|number_format:2:",":"."}</strong>
        </div>
    </section>

    <div class="bs-layout">
        <aside class="bs-side">
            <section class="pg-card bs-card">
                <div class="bs-section-head">
                    <span class="bs-kicker">Dati aziendali</span>
                    <h2>Profilo store</h2>
                </div>
                <div class="bs-data-list">
                    <div class="bs-data-row">
                        <span>Partita IVA</span>
                        <strong>{$business.p_iva|default:'Non indicata'}</strong>
                    </div>
                    <div class="bs-data-row">
                        <span>Email</span>
                        <strong>{$business.email_aziendale|default:'Non indicata'}</strong>
                    </div>
                    <div class="bs-data-row">
                        <span>Telefono</span>
                        <strong>{$business.telefono|default:'Non indicato'}</strong>
                    </div>
                    <div class="bs-data-row">
                        <span>Sede</span>
                        <strong>
                            {if !empty($business.via) || !empty($business.citta)}
                                {$business.via|default:''} {$business.numero|default:''}, {$business.cap|default:''} {$business.citta|default:''}{if !empty($business.provincia)} ({$business.provincia}){/if}
                            {else}
                                Da completare
                            {/if}
                        </strong>
                    </div>
                </div>
            </section>

            <section class="pg-card bs-card">
                <div class="bs-section-head">
                    <span class="bs-kicker">Vantaggi</span>
                    <h2>Vetrina PRO</h2>
                </div>
                <div class="bs-badges">
                    <span>Pagamenti protetti</span>
                    <span>Ordini tracciati</span>
                    <span>Feedback verificati</span>
                    <span>Catalogo curato</span>
                </div>
            </section>
        </aside>

        <main class="bs-main">
            <div class="pg-tabs bs-tabs" role="tablist" aria-label="Sezioni vetrina">
                <button class="pg-tab is-active" type="button" role="tab" aria-selected="true" data-bs-tab="annunci">
                    Annunci <span class="pg-tab-count">{$annunciTotali}</span>
                </button>
                <button class="pg-tab" type="button" role="tab" aria-selected="false" data-bs-tab="info">Info vetrina</button>
                {if !$isPublicVetrina}
                    <button class="pg-tab" type="button" role="tab" aria-selected="false" data-bs-tab="sede">Sede</button>
                {/if}
            </div>

            <section class="bs-panel is-active" id="vetrina-annunci" data-bs-panel="annunci">
                <div class="bs-toolbar">
                    <div class="or-filters" aria-label="Filtra annunci per stato">
                        <button class="or-filter is-on" type="button" data-bs-filter="tutti">Tutti <span class="or-filter-count">{$annunciTotali}</span></button>
                        <button class="or-filter" type="button" data-bs-filter="attivo">Attivi <span class="or-filter-count">{$annunciAttivi}</span></button>
                        <button class="or-filter" type="button" data-bs-filter="venduto">Venduti <span class="or-filter-count">{$annunciVenduti}</span></button>
                    </div>
                    <select class="pg-select bs-sort" aria-label="Ordina annunci" data-bs-sort>
                        <option value="recenti">Ordine originale</option>
                        <option value="prezzo-asc">Prezzo crescente</option>
                        <option value="prezzo-desc">Prezzo decrescente</option>
                        <option value="titolo">Titolo A-Z</option>
                    </select>
                </div>

                {if !empty($annunci)}
                    <div class="annunci-grid bs-annunci" data-bs-annunci>
                        {foreach $annunci as $annuncio}
                            {assign var=annuncioState value=$annuncio.stato|default:'attivo'|lower}
                            <div
                                class="bs-annuncio-wrap"
                                data-bs-annuncio
                                data-state="{$annuncioState}"
                                data-price="{$annuncio.prezzo|default:0}"
                                data-title="{$annuncio.titolo|default:''|lower}">
                                {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                            </div>
                        {/foreach}
                    </div>
                    <section class="pg-card bs-empty" data-bs-empty hidden>
                        <div class="bs-empty-mark">NV</div>
                        <h2>Nessun annuncio in questa vista</h2>
                        <p class="pg-sub">Cambia filtro oppure pubblica un nuovo articolo da collezione.</p>
                    </section>
                {else}
                    <section class="pg-card bs-empty">
                        <div class="bs-empty-mark">NV</div>
                        {if $isPublicVetrina}
                            <h2>Nessun annuncio disponibile</h2>
                            <p class="pg-sub">Questa vetrina non ha annunci attivi al momento.</p>
                        {else}
                            <h2>La tua vetrina e pronta</h2>
                            <p class="pg-sub">Aggiungi il primo annuncio per iniziare a costruire il catalogo.</p>
                            <a class="btn" href="/annuncio/create">Crea annuncio</a>
                        {/if}
                    </section>
                {/if}
            </section>

            <section class="bs-panel" data-bs-panel="info" hidden>
                <div class="pg-card">
                    <div class="bs-info-head">
                        <div>
                            <span class="bs-kicker">Info vetrina</span>
                            <h2>Presentazione pubblica</h2>
                        </div>
                        {if !$isPublicVetrina}
                            <button class="btn btn-secondary" type="button" data-bs-edit-info>Modifica info vetrina</button>
                        {/if}
                    </div>

                    <div class="bs-info-grid">
                        <div>
                            <span class="bs-kicker">Specializzazione</span>
                            <h2>{$businessName}</h2>
                            <p class="pg-sub">{$business.descrizione|default:'La vetrina raccoglie annunci selezionati, con informazioni chiare su condizioni, prezzo e disponibilita.'}</p>
                        </div>
                        <div>
                            <span class="bs-kicker">Spedizioni</span>
                            <h2>Gestione ordini</h2>
                            <p class="pg-sub">Gli ordini ricevuti sono consultabili dalla sezione dedicata, con stato e dettagli dell'acquirente.</p>
                        </div>
                        <div>
                            <span class="bs-kicker">Contatti</span>
                            <h2>Assistenza diretta</h2>
                            <p class="pg-sub">
                                {$business.email_aziendale|default:'Email non indicata'}{if !empty($business.telefono)} · {$business.telefono}{/if}
                                {if !empty($business.link_social)}<br><a href="{$business.link_social}" target="_blank" rel="noopener">Profilo social</a>{/if}
                            </p>
                        </div>
                    </div>

                    {if !$isPublicVetrina}
                        <form class="bs-info-form" method="post" action="/business/info-store" data-bs-info-form hidden>
                            <div class="pg-field-row">
                                <div class="pg-field">
                                    <label class="pg-label" for="nome_azienda">Nome vetrina</label>
                                    <input class="pg-input" type="text" id="nome_azienda" name="nome_azienda" value="{$business.nome_azienda|default:''}" minlength="2" maxlength="80" required>
                                </div>
                                <div class="pg-field">
                                    <label class="pg-label" for="email_aziendale">Email aziendale</label>
                                    <input class="pg-input" type="email" id="email_aziendale" name="email_aziendale" value="{$business.email_aziendale|default:''}" required>
                                </div>
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="descrizione">Descrizione vetrina</label>
                                <textarea class="pg-textarea" id="descrizione" name="descrizione" maxlength="500" placeholder="Racconta cosa vendi, che tipo di articoli tratti e come gestisci spedizioni e condizioni.">{$business.descrizione|default:''}</textarea>
                                <p class="pg-field-hint">Massimo 500 caratteri.</p>
                            </div>
                            <div class="pg-field-row">
                                <div class="pg-field">
                                    <label class="pg-label" for="telefono">Telefono</label>
                                    <input class="pg-input" type="tel" id="telefono" name="telefono" value="{$business.telefono|default:''}">
                                </div>
                                <div class="pg-field">
                                    <label class="pg-label" for="link_social">Link social</label>
                                    <input class="pg-input" type="url" id="link_social" name="link_social" value="{$business.link_social|default:''}" placeholder="https://...">
                                </div>
                            </div>
                            <div class="pg-form-actions">
                                <button type="submit" class="btn">Salva info vetrina</button>
                                <button type="button" class="btn btn-secondary" data-bs-cancel-info>Annulla</button>
                            </div>
                        </form>
                    {/if}
                </div>
            </section>

            {if !$isPublicVetrina}
            <section class="bs-panel" data-bs-panel="sede" hidden>
                <section class="pg-card bs-address-card">
                    <div class="bs-section-head">
                        <span class="bs-kicker">Indirizzo sede</span>
                        <h2>{if !empty($business.via)}Modifica sede{else}Aggiungi sede{/if}</h2>
                    </div>
                    <form method="post" action="/business/indirizzo-store" data-bs-address-form>
                        <div class="pg-field-row">
                            <div class="pg-field">
                                <label class="pg-label" for="via">Via / Corso / Piazza</label>
                                <input class="pg-input" type="text" id="via" name="via" value="{$business.via|default:''}" required>
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="numero">Numero civico</label>
                                <input class="pg-input" type="text" id="numero" name="numero" value="{$business.numero|default:''}">
                            </div>
                        </div>
                        <div class="pg-field-row">
                            <div class="pg-field">
                                <label class="pg-label" for="cap">CAP</label>
                                <input class="pg-input" type="text" id="cap" name="cap" maxlength="5" value="{$business.cap|default:''}">
                            </div>
                            <div class="pg-field">
                                <label class="pg-label" for="citta">Citta</label>
                                <input class="pg-input" type="text" id="citta" name="citta" value="{$business.citta|default:''}" required>
                            </div>
                        </div>
                        <div class="pg-field">
                            <label class="pg-label" for="provincia">Provincia</label>
                            <input class="pg-input" type="text" id="provincia" name="provincia" maxlength="2" value="{$business.provincia|default:''}">
                        </div>
                        <div class="pg-form-actions">
                            <button type="submit" class="btn">Salva indirizzo</button>
                            <button type="button" class="btn btn-secondary" data-bs-tab="annunci">Torna agli annunci</button>
                        </div>
                    </form>
                </section>
            </section>
            {/if}
        </main>
    </div>

    <script>
    (function () {
        const tabs = Array.from(document.querySelectorAll('[data-bs-tab]'));
        const panels = Array.from(document.querySelectorAll('[data-bs-panel]'));
        const filterButtons = Array.from(document.querySelectorAll('[data-bs-filter]'));
        const cards = Array.from(document.querySelectorAll('[data-bs-annuncio]'));
        const emptyState = document.querySelector('[data-bs-empty]');
        const sortSelect = document.querySelector('[data-bs-sort]');
        const grid = document.querySelector('[data-bs-annunci]');

        function openTab(name) {
            tabs.forEach(function (tab) {
                const isActive = tab.dataset.bsTab === name;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            panels.forEach(function (panel) {
                const isActive = panel.dataset.bsPanel === name;
                panel.hidden = !isActive;
                panel.classList.toggle('is-active', isActive);
            });
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                openTab(tab.dataset.bsTab);
            });
        });

        document.querySelectorAll('[data-bs-toggle-address]').forEach(function (button) {
            button.addEventListener('click', function () {
                openTab('sede');
                document.querySelector('[data-bs-address-form]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        document.querySelectorAll('[data-bs-edit-info]').forEach(function (button) {
            button.addEventListener('click', function () {
                openTab('info');
                const form = document.querySelector('[data-bs-info-form]');
                if (form) {
                    form.hidden = false;
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        document.querySelectorAll('[data-bs-cancel-info]').forEach(function (button) {
            button.addEventListener('click', function () {
                const form = button.closest('[data-bs-info-form]');
                if (form) {
                    form.hidden = true;
                }
            });
        });

        function applyFilter(filter) {
            let visible = 0;
            cards.forEach(function (card) {
                const show = filter === 'tutti' || card.dataset.state === filter;
                card.hidden = !show;
                if (show) visible += 1;
            });
            if (emptyState) {
                emptyState.hidden = visible !== 0;
            }
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                filterButtons.forEach(function (item) { item.classList.remove('is-on'); });
                button.classList.add('is-on');
                applyFilter(button.dataset.bsFilter);
            });
        });

        if (sortSelect && grid) {
            sortSelect.addEventListener('change', function () {
                const sorted = cards.slice().sort(function (a, b) {
                    if (sortSelect.value === 'prezzo-asc') {
                        return Number(a.dataset.price || 0) - Number(b.dataset.price || 0);
                    }
                    if (sortSelect.value === 'prezzo-desc') {
                        return Number(b.dataset.price || 0) - Number(a.dataset.price || 0);
                    }
                    if (sortSelect.value === 'titolo') {
                        return (a.dataset.title || '').localeCompare(b.dataset.title || '');
                    }
                    return cards.indexOf(a) - cards.indexOf(b);
                });
                sorted.forEach(function (card) { grid.appendChild(card); });
            });
        }
    })();
    </script>
{else}
    <section class="pg-card bs-empty bs-empty-wide">
        <div class="bs-empty-mark">NV</div>
        <h1>Attiva la tua vetrina</h1>
        <p class="pg-sub">Crea un account business per pubblicare annunci, gestire ordini e mostrare il tuo catalogo in stile NerdVault.</p>
        <a class="btn" href="/business/create">Crea account business</a>
    </section>
{/if}

{include file="layouts/footer.tpl"}
