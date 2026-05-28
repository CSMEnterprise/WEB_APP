{* Lista annunci: mostra tutti gli annunci disponibili o i risultati di una ricerca per testo/categoria. Se la ricerca restituisce anche utenti, li mostra in una sezione separata. *}
{include file="layouts/header.tpl"}

<div class="u-style-007">
    <h1 class="u-style-008">{if $q != '' || $idCategoria > 0}Risultati ricerca{else}Annunci disponibili{/if}</h1>

    {if $isLogged && !$isAdmin}
        <a class="btn" href="index.php?route=annuncio-create">Crea annuncio</a>
    {/if}
</div>

{if $q != '' || $idCategoria > 0}
    <p class="muted">
        {if $q != ''}Testo: "{$q}"{/if}
        {if $idCategoria > 0}
            {assign var=categoriaSelezionata value=''}
            {foreach $categorie as $categoria}
                {if $categoria.id_categoria == $idCategoria}
                    {assign var=categoriaSelezionata value=$categoria.nome}
                {/if}
            {/foreach}
            {if $q != ''} - {/if}Categoria: {$categoriaSelezionata}
        {/if}
    </p>
{/if}

{if $q != '' && !empty($utenti)}
    <section class="u-style-002">
        <h2>Utenti trovati</h2>
        <div class="grid">
            {foreach $utenti as $utente}
                {include file="components/utente_card.tpl" utente=$utente}
            {/foreach}
        </div>
    </section>
{/if}

<section>
    {if $q != ''}<h2>Annunci trovati</h2>{/if}

    {if !empty($annunci)}
        <div class="grid">
            {foreach $annunci as $annuncio}
                {include file="components/annuncio_card.tpl" annuncio=$annuncio wishlistIds=$wishlistIds carrelloIds=$carrelloIds}
            {/foreach}
        </div>
    {else}
        <div class="card">
            {if $q != ''}
                <p>Nessun annuncio trovato per "{$q}".</p>
            {else}
                <p>Nessun annuncio disponibile.</p>
            {/if}
        </div>
    {/if}
</section>

{include file="layouts/footer.tpl"}
