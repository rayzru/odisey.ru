{if !empty($articles) && count($articles)}
<ul class="catalog-item__articles">
    {section name="id" loop="`$articles`"}
        <li><a class="catalog-item__article-link" href="/feed/{$articles[id].id}-{$articles[id].slug}">{$articles[id].title}</a></li>
    {/section}
</ul>
{/if}