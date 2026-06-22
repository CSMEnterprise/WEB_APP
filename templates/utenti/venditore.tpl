{* Profilo pubblico venditore: mostra avatar, statistiche, feedback e annunci attivi. *}
{include file="layouts/header.tpl"}

{if !empty($venditore)}
    {assign var=displayName value=$venditore.username|default:'Venditore'}
    {assign var=annunciCount value=$annunciVenditore|count_items}
    {assign var=feedbackCount value=$feedbackVenditore|count_items}
    {assign var=stelle value=$mediaVenditore|default:0|round}

    <nav class="pg-breadcrumb">
        <a href="/home/index">Home</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="current">{$displayName}</span>
    </nav>

    <section class="pg-card vd-hero" aria-label="Riepilogo venditore">
        <div class="vd-avatar">
            {if !empty($venditore.propic)}
                <img class="pg-avatar" src="{$venditore.propic}" alt="Foto profilo">
            {else}
                <span class="pg-avatar-fallback">{$displayName|substr:0:1|strtoupper}</span>
            {/if}
            <span class="pg-pill" data-tone="accent">Profilo pubblico</span>
        </div>

        <div class="vd-summary">
            <span class="pg-pill">Account personale</span>
            <h1 class="pg-h1">{$displayName}</h1>
            <p class="pg-sub">Visualizza reputazione, feedback e annunci attualmente disponibili da questo venditore.</p>

            <div class="pg-info-grid">
                <div class="pg-info-item">
                    <span>Username</span>
                    <strong>{$venditore.username|default:'Non indicato'}</strong>
                </div>
                <div class="pg-info-item">
                    <span>Registrato dal</span>
                    <strong>{if !empty($venditore.data_registrazione)}{$venditore.data_registrazione|date_it}{else}Non indicato{/if}</strong>
                </div>
                <div class="pg-info-item">
                    <span>Feedback</span>
                    <strong>{if $feedbackCount > 0}{$mediaVenditore|number_format:1:",":"."} / 5{else}Nessuna recensione{/if}</strong>
                </div>
            </div>
        </div>

        <div class="pg-stats vd-stats">
            <div class="pg-stat">
                <p class="pg-stat-val">{$annunciCount}</p>
                <p class="pg-stat-label">Annunci attivi</p>
            </div>
            <div class="pg-stat">
                <p class="pg-stat-val">{if $feedbackCount > 0}{$mediaVenditore|number_format:1:",":"."}{else}-{/if}</p>
                <p class="pg-stat-label">Valutazione</p>
            </div>
        </div>
    </section>

    <div class="pg-actions vd-actions">
        <a class="btn btn-secondary" href="/feedback/venditore/{$venditore.id_utente|default:0}">Vedi feedback</a>
    </div>

    <section class="vd-annunci">
        <div class="section-head">
            <div>
                <h2>Annunci in vendita</h2>
                <p class="muted">Oggetti pubblicati e disponibili da questo venditore.</p>
            </div>
        </div>

        {if !empty($annunciVenditore)}
            <div class="grid">
                {foreach $annunciVenditore as $annuncio}
                    {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                {/foreach}
            </div>
        {else}
            <div class="pg-card"><p class="muted" style="margin:0;">Questo venditore non ha annunci attivi.</p></div>
        {/if}
    </section>
{else}
    <div class="pg-alert" data-tone="danger">Venditore non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
