{include file="layouts/header.tpl"}

<section class="card">
    <h1>Errore</h1>
    <div class="alert alert-error">
        {$errore|default:'Richiesta non valida.'}
    </div>
    <p><a class="btn" href="index.php?route=home">Torna alla home</a></p>
</section>

{include file="layouts/footer.tpl"}
