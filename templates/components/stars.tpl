<span aria-label="Valutazione {$value|default:0} su 5">
    {for $star=1 to 5}{if $star <= ($value|default:0)}&#9733;{else}&#9734;{/if}{/for}
</span>
