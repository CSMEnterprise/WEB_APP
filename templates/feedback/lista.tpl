{include file="layouts/header.tpl"}

{assign var=feedbackTotale value=$feedback|default:[]|count}
{assign var=sommaFeedback value=0}
{assign var=star5 value=0}
{assign var=star4 value=0}
{assign var=star3 value=0}
{assign var=star2 value=0}
{assign var=star1 value=0}
{foreach $feedback|default:[] as $stat}
    {assign var=vote value=$stat.valutazione|default:0}
    {assign var=sommaFeedback value=$sommaFeedback+$vote}
    {if $vote == 5}{assign var=star5 value=$star5+1}{/if}
    {if $vote == 4}{assign var=star4 value=$star4+1}{/if}
    {if $vote == 3}{assign var=star3 value=$star3+1}{/if}
    {if $vote == 2}{assign var=star2 value=$star2+1}{/if}
    {if $vote == 1}{assign var=star1 value=$star1+1}{/if}
{/foreach}
{assign var=mediaFeedback value=0}
{if $feedbackTotale > 0}{assign var=mediaFeedback value=$sommaFeedback/$feedbackTotale}{/if}

<nav class="pg-breadcrumb" aria-label="Percorso">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span class="current">Feedback</span>
</nav>

<div class="fb-layout fb-layout-wide">
    <main class="fb-main">
        <section class="fb-summary" aria-label="Riepilogo feedback">
            <div class="fb-score">
                <span class="fb-score-num">{$mediaFeedback|number_format:1:",":"."}</span>
                <span class="fb-stars">{include file="components/stars.tpl" value=$mediaFeedback|round}</span>
                <span class="fb-score-sub">{$feedbackTotale} feedback</span>
            </div>
            <div class="fb-bars" aria-label="Distribuzione voti">
                <button class="fb-bar-row" type="button" data-fb-filter="5"><span class="fb-bar-label">5 &#9733;</span><span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($star5/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span><span class="fb-bar-n">{$star5}</span></button>
                <button class="fb-bar-row" type="button" data-fb-filter="4"><span class="fb-bar-label">4 &#9733;</span><span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($star4/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span><span class="fb-bar-n">{$star4}</span></button>
                <button class="fb-bar-row" type="button" data-fb-filter="3"><span class="fb-bar-label">3 &#9733;</span><span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($star3/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span><span class="fb-bar-n">{$star3}</span></button>
                <button class="fb-bar-row" type="button" data-fb-filter="2"><span class="fb-bar-label">2 &#9733;</span><span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($star2/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span><span class="fb-bar-n">{$star2}</span></button>
                <button class="fb-bar-row" type="button" data-fb-filter="1"><span class="fb-bar-label">1 &#9733;</span><span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($star1/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span><span class="fb-bar-n">{$star1}</span></button>
            </div>
        </section>

        <section class="fb-list">
            <div class="fb-list-head">
                <div>
                    <h1 class="pg-h1">I miei feedback</h1>
                    <p class="pg-sub">Feedback scritti o ricevuti nelle tue transazioni.</p>
                </div>
                <div class="fb-tools">
                    <button class="or-filter is-on" type="button" data-fb-filter="all">Tutti</button>
                    <select class="pg-select fb-sort" data-fb-sort aria-label="Ordina feedback">
                        <option value="recenti">Piu recenti</option>
                        <option value="alte">Valutazione alta</option>
                        <option value="basse">Valutazione bassa</option>
                    </select>
                </div>
            </div>

            {if !empty($feedback)}
                <div class="fb-reviews" data-fb-list>
                    {foreach $feedback as $item}
                        {assign var=itemVote value=$item.valutazione|default:0}
                        <article class="fb-review" data-fb-review data-rating="{$itemVote}" data-date="{$item.data_feedback|default:''}">
                            <span class="fb-review-av">{$item.autore|default:'U'|substr:0:1|strtoupper}</span>
                            <div class="fb-review-body">
                                <div class="fb-review-head">
                                    <strong>{$item.autore|default:'Utente'}</strong>
                                    <span class="fb-stars">{include file="components/stars.tpl" value=$itemVote}</span>
                                    <span class="pg-sub fb-review-date">{$item.data_feedback|default:''}</span>
                                </div>
                                <p class="fb-review-anno">su {$item.titolo|default:'Annuncio'}</p>
                                {if !empty($item.commento)}
                                    <p class="fb-review-text">{$item.commento}</p>
                                {else}
                                    <p class="fb-review-text">Feedback senza commento testuale.</p>
                                {/if}
                            </div>
                        </article>
                    {/foreach}
                </div>
                <section class="pg-card fb-empty" data-fb-empty hidden>
                    <div class="fb-empty-mark">&#9733;</div>
                    <h2>Nessun feedback per questo filtro</h2>
                    <p class="pg-sub">Cambia valutazione o torna alla vista completa.</p>
                </section>
            {else}
                <section class="pg-card fb-empty">
                    <div class="fb-empty-mark">&#9733;</div>
                    <h2>Non sono presenti feedback</h2>
                    <p class="pg-sub">Quando ricevi o lasci una recensione, la trovi qui.</p>
                </section>
            {/if}
        </section>
    </main>
</div>

<script>
(function () {
    const reviews = Array.from(document.querySelectorAll('[data-fb-review]'));
    const list = document.querySelector('[data-fb-list]');
    const empty = document.querySelector('[data-fb-empty]');
    const filters = Array.from(document.querySelectorAll('[data-fb-filter]'));
    const sort = document.querySelector('[data-fb-sort]');

    function applyFilter(value) {
        let visible = 0;
        reviews.forEach(function (review) {
            const show = value === 'all' || review.dataset.rating === value;
            review.hidden = !show;
            if (show) visible += 1;
        });
        if (empty) {
            empty.hidden = visible !== 0;
        }
    }

    filters.forEach(function (button) {
        button.addEventListener('click', function () {
            filters.forEach(function (item) { item.classList.remove('is-on'); });
            button.classList.add('is-on');
            applyFilter(button.dataset.fbFilter);
        });
    });

    if (sort && list) {
        sort.addEventListener('change', function () {
            const sorted = reviews.slice().sort(function (a, b) {
                if (sort.value === 'alte') {
                    return Number(b.dataset.rating || 0) - Number(a.dataset.rating || 0);
                }
                if (sort.value === 'basse') {
                    return Number(a.dataset.rating || 0) - Number(b.dataset.rating || 0);
                }
                return reviews.indexOf(a) - reviews.indexOf(b);
            });
            sorted.forEach(function (review) { list.appendChild(review); });
        });
    }
})();
</script>

{include file="layouts/footer.tpl"}
