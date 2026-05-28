{* Area business: mostra dati aziendali, form toggle per aggiungere/modificare l'indirizzo sede, lista annunci propri con accesso rapido ai dettagli e al pannello ordini. *}
{include file="layouts/header.tpl"}

<h1>Area business</h1>

{if !empty($errore)}
    <div class="alert alert-error">{$errore}</div>
{/if}

{if !empty($business)}
    <section class="card">
        <h2>{$business.nome_azienda|default:''}</h2>
        <p><strong>Partita IVA:</strong> {$business.p_iva|default:''}</p>
        <p><strong>Email:</strong> {$business.email_aziendale|default:''}</p>
        <p><strong>Telefono:</strong> {$business.telefono|default:''}</p>
        <p><strong>Verificato:</strong> {if !empty($business.verificato)}Si{else}No{/if}</p>
        {if !empty($business.via) || !empty($business.citta)}
            <hr>
            <p><strong>Sede:</strong> {$business.via|default:''} {$business.numero|default:''}, {$business.cap|default:''} {$business.citta|default:''}{if !empty($business.provincia)} ({$business.provincia}){/if}</p>
        {/if}
    </section>

    <p>
        <button type="button" class="btn btn-secondary" onclick="toggleIndirizzoForm()">
            {if !empty($business.via)}Modifica indirizzo sede{else}Aggiungi indirizzo sede{/if}
        </button>
    </p>

    <div id="indirizzoForm" class="card u-style-013">
        <h2>Indirizzo sede</h2>
        <form method="post" action="/business/indirizzo-store">
                        <label for="via">Via / Corso / Piazza</label>
            <input type="text" id="via" name="via" value="{$business.via|default:''}" required>

            <label for="numero">Numero civico</label>
            <input type="text" id="numero" name="numero" value="{$business.numero|default:''}">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" maxlength="5" value="{$business.cap|default:''}">

            <label for="citta">Citta</label>
            <input type="text" id="citta" name="citta" value="{$business.citta|default:''}" required>

            <label for="provincia">Provincia</label>
            <input type="text" id="provincia" name="provincia" maxlength="2" value="{$business.provincia|default:''}">

            <button type="submit" class="btn">Salva indirizzo</button>
        </form>
    </div>

    <h2>I miei annunci</h2>
    {if !empty($annunci)}
        <table>
            <thead>
                <tr><th>Titolo</th><th>Prezzo</th><th>Stato</th><th>Azioni</th></tr>
            </thead>
            <tbody>
                {foreach $annunci as $annuncio}
                    <tr>
                        <td>{$annuncio.titolo|default:''}</td>
                        <td>&euro; {$annuncio.prezzo|default:0|number_format:2:",":"."}</td>
                        <td>{$annuncio.stato|default:''}</td>
                        <td>
                            {if ($annuncio.stato|default:'') == 'attivo'}
                                <a class="btn btn-secondary" href="/annuncio/edit/{$annuncio.id_annuncio|default:0}">Modifica</a>
                            {/if}
                            <a class="btn" href="/annuncio/show/{$annuncio.id_annuncio|default:0}">Dettagli</a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <p>Nessun annuncio pubblicato.</p>
    {/if}

    <p><a class="btn" href="/business/ordini">Ordini ricevuti</a></p>

    <script>
    function toggleIndirizzoForm() {
        const form = document.getElementById('indirizzoForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    </script>
{else}
    <div class="card">
        <p>Non hai ancora un account business.</p>
        <a class="btn" href="/business/create">Crea account business</a>
    </div>
{/if}

{include file="layouts/footer.tpl"}
