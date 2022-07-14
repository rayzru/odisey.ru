{foreach from=$site->scripts key=k item=script}{if !$script.inHead}<script src="{$script.src}" {if $script.async}async="async"{/if} {if $script.defer}defer{/if}></script>
{/if}{/foreach}
</body>
</html>
