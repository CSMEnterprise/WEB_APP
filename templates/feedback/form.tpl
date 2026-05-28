{* Form inserimento feedback: valutazione 1-5 stelle con anteprima interattiva via JS e campo commento. Collegato a un pagamento completato tramite id_pagamento. *}
{include file="layouts/header.tpl"}

<div class="card">
    <h1>Lascia un feedback</h1>

    {if !empty($pagamento)}
        <p>Stai valutando l'acquisto di <strong>{$pagamento.titolo|default:$pagamento.annuncio_titolo|default:'questo prodotto'}</strong>.</p>
    {/if}

    {if !empty($errore)}
        <div class="alert alert-error">{$errore}</div>
    {/if}

    <form method="post" action="/feedback/store">
        <input type="hidden" name="id_pagamento" value="{$idPagamento|default:$pagamento.id_pagamento|default:0}">

        <label>Valutazione</label>
        <div class="u-style-014">
            {for $i=1 to 5}
                <label class="u-style-015">
                    <input class="u-style-013" type="radio" name="valutazione" value="{$i}"
                        {if ($post.valutazione|default:0) == $i}checked{/if}
                        required>
                    <span class="star" data-val="{$i}">&#9733;</span>
                </label>
            {/for}
        </div>

        <label for="commento">Commento</label>
        <textarea id="commento" name="commento" placeholder="Descrivi la tua esperienza...">{$post.commento|default:''}</textarea>

        <button class="btn" type="submit">Invia feedback</button>
        <a class="btn btn-secondary" href="/utente/profilo">Annulla</a>
    </form>
</div>

<script>
const stars = document.querySelectorAll('.star');
stars.forEach(star => {
    star.style.color = '#d1d5db';
    star.addEventListener('mouseenter', () => {
        const val = +star.dataset.val;
        stars.forEach(s => s.style.color = +s.dataset.val <= val ? '#f59e0b' : '#d1d5db');
    });
    star.addEventListener('mouseleave', updateStars);
    star.addEventListener('click', () => {
        star.previousElementSibling.checked = true;
        updateStars();
    });
});

function updateStars() {
    const checked = document.querySelector('input[name="valutazione"]:checked');
    const val = checked ? +checked.value : 0;
    stars.forEach(s => s.style.color = +s.dataset.val <= val ? '#f59e0b' : '#d1d5db');
}

updateStars();
</script>

{include file="layouts/footer.tpl"}
