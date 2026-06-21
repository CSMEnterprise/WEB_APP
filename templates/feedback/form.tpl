{include file="layouts/header.tpl"}

<nav class="pg-breadcrumb" aria-label="Percorso">
    <a href="/utente/profilo">Profilo</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span class="current">Feedback</span>
</nav>

<div class="fb-layout">
    <main class="fb-main">
        <section class="fb-summary fb-form-hero">
            <div class="fb-score">
                <span class="fb-score-num" data-fb-live-score>{$post.valutazione|default:0}</span>
                <span class="fb-score-sub">La tua valutazione</span>
            </div>
            <div>
                <span class="bs-kicker">Feedback verificato</span>
                <h1 class="pg-h1">Lascia un feedback</h1>
                <p class="pg-sub">
                    {if !empty($pagamento)}
                        Stai valutando l'acquisto di <strong>{$pagamento.titolo|default:$pagamento.annuncio_titolo|default:'questo prodotto'}</strong>.
                    {else}
                        Racconta come e andata la transazione.
                    {/if}
                </p>
            </div>
        </section>

        {if !empty($errore)}
            <div class="pg-alert" data-tone="danger">{$errore}</div>
        {/if}

        <form class="pg-card fb-submit-form" method="post" action="/feedback/store" data-fb-form>
            <input type="hidden" name="{$csrfField}" value="{$csrfToken}">
            <input type="hidden" name="id_pagamento" value="{$idPagamento|default:$pagamento.id_pagamento|default:0}">

            <div class="pg-field">
                <label class="pg-label">Valutazione</label>
                <div class="fb-stars-pick" data-fb-stars>
                    {for $i=1 to 5}
                        <label class="fb-star-option">
                            <input type="radio" name="valutazione" value="{$i}" {if ($post.valutazione|default:0) == $i}checked{/if} required>
                            <span class="fb-star" data-val="{$i}" aria-label="{$i} stelle">&#9733;</span>
                        </label>
                    {/for}
                </div>
                <span class="fb-stars-label" data-fb-stars-label>Tocca per valutare</span>
            </div>

            <div class="pg-field">
                <label class="pg-label" for="commento">Commento</label>
                <textarea class="pg-textarea" id="commento" name="commento" maxlength="700" placeholder="Descrivi la tua esperienza: imballaggio, tempi, corrispondenza con l'annuncio..." data-fb-comment>{$post.commento|default:''}</textarea>
                <p class="pg-field-hint"><span data-fb-count>0</span>/700 caratteri</p>
            </div>

            <div class="pg-form-actions">
                <button class="btn" type="submit">Invia feedback</button>
                <a class="btn btn-secondary" href="/utente/profilo">Annulla</a>
            </div>
        </form>
    </main>

    <aside class="fb-side">
        <section class="fb-form fb-side-card">
            <h2 class="fb-form-title">Suggerimenti</h2>
            <p class="pg-sub">Una recensione utile parla di condizioni, imballaggio, tempi e comunicazione.</p>
            <div class="fb-tips">
                <span>Condizioni coerenti</span>
                <span>Imballaggio protetto</span>
                <span>Spedizione e tempi</span>
                <span>Comunicazione</span>
            </div>
        </section>
    </aside>
</div>

<script>
(function () {
    const stars = Array.from(document.querySelectorAll('.fb-star'));
    const inputs = Array.from(document.querySelectorAll('input[name="valutazione"]'));
    const label = document.querySelector('[data-fb-stars-label]');
    const score = document.querySelector('[data-fb-live-score]');
    const comment = document.querySelector('[data-fb-comment]');
    const counter = document.querySelector('[data-fb-count]');
    const labels = ['', 'Pessimo', 'Scarso', 'Nella media', 'Buono', 'Eccellente'];

    function selectedValue() {
        const checked = inputs.find(function (input) { return input.checked; });
        return checked ? Number(checked.value) : 0;
    }

    function paint(value) {
        stars.forEach(function (star) {
            star.classList.toggle('is-on', Number(star.dataset.val) <= value);
        });
        if (label) {
            label.textContent = value ? labels[value] : 'Tocca per valutare';
        }
        if (score) {
            score.textContent = String(value || 0);
        }
    }

    stars.forEach(function (star) {
        star.addEventListener('mouseenter', function () { paint(Number(star.dataset.val)); });
        star.addEventListener('mouseleave', function () { paint(selectedValue()); });
        star.addEventListener('click', function () {
            const input = star.closest('label')?.querySelector('input');
            if (input) {
                input.checked = true;
                paint(Number(input.value));
            }
        });
    });

    if (comment && counter) {
        const updateCount = function () { counter.textContent = String(comment.value.length); };
        comment.addEventListener('input', updateCount);
        updateCount();
    }

    paint(selectedValue());
})();
</script>

{include file="layouts/footer.tpl"}
