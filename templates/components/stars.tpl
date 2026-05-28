{* Componente stelle rating: riceve $value (1-5, intero) e stampa stelle piene/vuote con aria-label per accessibilità. Usato nei dettagli annuncio e nei feedback venditore. *}
<span aria-label="Valutazione {$value|default:0} su 5">
    {for $star=1 to 5}{if $star <= ($value|default:0)}&#9733;{else}&#9734;{/if}{/for}
</span>
