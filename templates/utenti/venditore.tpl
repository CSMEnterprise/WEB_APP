{* Profilo pubblico venditore: mostra avatar, statistiche (annunci attivi, media feedback), e grid degli annunci attualmente in vendita. Solo lettura, senza azioni di modifica. *}
{include file="layouts/header.tpl"}

{if !empty($venditore)}
    {assign var=displayName value=$venditore.username|default:'Venditore'}
    {assign var=annunciCount value=$annunciVenditore|count_items}
    {assign var=feedbackCount value=$feedbackVenditore|count_items}
    {assign var=stelle value=$mediaVenditore|default:0|round}

    <div class="profile-page">
        <section class="profile-hero" aria-label="Riepilogo venditore">
            <div class="profile-avatar-form">
                <div class="profile-avatar-button" aria-hidden="true">
                    {if !empty($venditore.propic)}
                        <img src="{$venditore.propic}" alt="Foto profilo">
                    {else}
                        <span class="profile-avatar-initial">{$displayName|substr:0:1|strtoupper}</span>
                    {/if}
                </div>
                <p class="profile-avatar-hint">Profilo pubblico</p>
            </div>

            <div class="profile-summary">
                <span class="profile-kicker">Account venditore</span>
                <h1>{$displayName}</h1>
                <p class="profile-summary-copy">Visualizza il profilo del venditore e gli annunci attualmente in vendita.</p>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <span>Username</span>
                        <strong>{$venditore.username|default:'Non indicato'}</strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Registrato dal</span>
                        <strong>{if !empty($venditore.data_registrazione)}{$venditore.data_registrazione|date_it}{else}Non indicato{/if}</strong>
                    </div>
                </div>
            </div>

            <div class="profile-stats" aria-label="Statistiche venditore">
                <div class="profile-stat">
                    <span class="profile-stat-value">{$annunciCount}</span>
                    <span class="profile-stat-label">Annunci attivi</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-value">{if $feedbackCount > 0}{$mediaVenditore|number_format:1:",":"."}{else}-{/if}</span>
                    <span class="profile-stat-label">Feedback</span>
                </div>
            </div>
        </section>

        <div class="profile-actions">
            <a class="btn btn-secondary" href="index.php?route=feedback-venditore&id={$venditore.id_utente|default:0}">Vedi feedback</a>
        </div>

        <section class="profile-annunci">
            <div class="profile-section-header">
                <div>
                    <h2>Annunci in vendita</h2>
                    <p>Oggetti pubblicati e disponibili da questo venditore.</p>
                </div>
            </div>

            {if !empty($annunciVenditore)}
                <div class="grid profile-grid">
                    {foreach $annunciVenditore as $annuncio}
                        {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=[] carrelloIds=[]}
                    {/foreach}
                </div>
            {else}
                <div class="card profile-empty"><p>Questo venditore non ha annunci attivi.</p></div>
            {/if}
        </section>
    </div>
{else}
    <div class="alert alert-error">Venditore non trovato.</div>
{/if}

{include file="layouts/footer.tpl"}
