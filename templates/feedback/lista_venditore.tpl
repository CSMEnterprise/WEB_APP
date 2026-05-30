{include file="layouts/header.tpl"}

{assign var=feedbackTotale value=$feedback|default:[]|count}
{assign var=mediaFeedback value=$media|default:0}
{assign var=displayName value=$venditore.username|default:'Venditore'}
{assign var=star5 value=0}
{assign var=star4 value=0}
{assign var=star3 value=0}
{assign var=star2 value=0}
{assign var=star1 value=0}
{foreach $feedback|default:[] as $stat}
    {assign var=vote value=$stat.valutazione|default:0}
    {if $vote == 5}{assign var=star5 value=$star5+1}{/if}
    {if $vote == 4}{assign var=star4 value=$star4+1}{/if}
    {if $vote == 3}{assign var=star3 value=$star3+1}{/if}
    {if $vote == 2}{assign var=star2 value=$star2+1}{/if}
    {if $vote == 1}{assign var=star1 value=$star1+1}{/if}
{/foreach}

<nav class="pg-breadcrumb" aria-label="Percorso">
    <a href="/home/index">Home</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <a href="/utente/venditore/{$venditore.id_utente|default:0}">{$displayName}</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span class="current">Feedback</span>
</nav>

<div class="fb-layout">
    <main class="fb-main">
        <section class="fb-summary" aria-label="Riepilogo feedback">
            <div class="fb-score">
                <span class="fb-score-num">{$mediaFeedback|number_format:1:",":"."}</span>
                <span class="fb-stars" aria-label="Media feedback">{include file="components/stars.tpl" value=$mediaFeedback|round}</span>
                <span class="fb-score-sub">{$feedbackTotale} recensioni</span>
            </div>

            <div class="fb-bars" aria-label="Distribuzione voti">
                {foreach [
                    ['stelle'=>5, 'n'=>$star5],
                    ['stelle'=>4, 'n'=>$star4],
                    ['stelle'=>3, 'n'=>$star3],
                    ['stelle'=>2, 'n'=>$star2],
                    ['stelle'=>1, 'n'=>$star1]
                ] as $row}
                    <button class="fb-bar-row" type="button" data-fb-filter="{$row.stelle}">
                        <span class="fb-bar-label">{$row.stelle} &#9733;</span>
                        <span class="fb-bar-track"><span class="fb-bar-fill" style="width: {if $feedbackTotale > 0}{($row.n/$feedbackTotale*100)|round}%{else}0%{/if};"></span></span>
                        <span class="fb-bar-n">{$row.n}</span>
                    </button>
                {/foreach}
            </div>

            <div class="fb-seller">
                <span class="fb-seller-av">{$displayName|substr:0:1|strtoupper}</span>
                <div>
                    <strong>{$displayName} <span class="nv-pro-badge">PRO</span></strong>
                    <span class="pg-sub">Feedback verificati dagli acquisti completati</span>
                </div>
            </div>
        </section>

        <section class="fb-list">
            <div class="fb-list-head">
                <div>
                    <h1 class="pg-h1">Recensioni recenti</h1>
                    <p class="pg-sub">Esperienze reali lasciate dagli acquirenti.</p>
                </div>
                <div class="fb-tools">
                    <button class="or-filter is-on" type="button" data-fb-filter="all">Tutte</button>
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
                        <article
                            class="fb-review"
                            data-fb-review
                            data-rating="{$itemVote}"
                            data-date="{$item.data_feedback|default:''}">
                            <span class="fb-review-av">{$item.autore|default:'U'|substr:0:1|strtoupper}</span>
                            <div class="fb-review-body">
                                <div class="fb-review-head">
                                    <strong>{$item.autore|default:'Utente'}</strong>
                                    <span class="fb-stars">{include file="components/stars.tpl" value=$itemVote}</span>
                                    <span class="pg-sub fb-review-date">{$item.data_feedback|default:''}</span>
                                </div>
                                <p class="fb-review-anno">su <a href="/annuncio/show/{$item.annuncio_id|default:0}">{$item.annuncio_titolo|default:'Annuncio'}</a></p>
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
                    <h2>Nessun feedback ricevuto</h2>
                    <p class="pg-sub">Le recensioni appariranno qui dopo gli acquisti completati.</p>
                </section>
            {/if}
        </section>
    </main>

    <aside class="fb-side">
        <section class="fb-form fb-side-card">
            <h2 class="fb-form-title">Rating venditore</h2>
            <p class="pg-sub">La media e calcolata solo sui feedback collegati a pagamenti completati.</p>
            <div class="fb-side-score">
                <strong>{$mediaFeedback|number_format:1:",":"."}</strong>
                <span>{include file="components/stars.tpl" value=$mediaFeedback|round}</span>
            </div>
            <a class="btn btn-secondary btn-block" href="/utente/venditore/{$venditore.id_utente|default:0}">Torna alla vetrina</a>
        </section>
    </aside>
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
