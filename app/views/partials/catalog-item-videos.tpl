{if !empty($item.videos) && count($item.videos)}
<div class="catalog-item__videos">
    {section name="id" loop="`$item.videos`"}
        <div class="catalog-item__video">
            {$item.videos[id].url|video}
        </div>
    {/section}
</div>
{/if}