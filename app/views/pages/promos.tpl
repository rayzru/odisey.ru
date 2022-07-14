<div class="container">
    <h1 class="page-title">Акции</h1>
    {if count($promo)}
    <div class="promo-list">
        {section name="id" loop="$promo"}
            {if $promo[id].current == 1}
                <a href="/promo/{$promo[id].id}-{$promo[id].slug}/" class="promo-list__card">
                    <h4 class="promo-list__title">{$promo[id].title}</h4>
                    <span class="promo-list__due">{$promo[id].date_start|date_format:"%d %B"} - {$promo[id].date_end|date_format:"%d %B"}</span>
                </a>
            {/if}
        {/section}
    </div>
    <div class="promo-list-old">
        <h3 class="promo-list-old__header">Прошедшие акции</h3>
        {section name="i" loop="$promo"}
            {if $promo[i].current == 0}
                <div>
                    <a href="/promo/{$promo[i].id}-{$promo[i].slug}/">
                        {$promo[i].title}
                        <small class="text-muted">
                            {$promo[i].date_start|date_format:"%d %B"} -
                            {$promo[i].date_end|date_format:"%d %B"}
                        </small>
                    </a>
                </div>
            {/if}
        {/section}
    </div>
    {else}
        В данный момент акции отсутствуют
    {/if}
</div>
