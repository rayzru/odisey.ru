<div class="container">
    {include file="partials/breadcrumbs.tpl"}
    <h1 class="page-title">{$category.title}</h1>
    {if !empty($articles) || !empty($category.description)}
        <section class="bg-lightest">
            <div class="container pt-3 pb-3 mb-5">

                {if !empty($category.description)}{$category.description}{/if}

                {if !empty($articles)}
                    <h4>Статьи</h4>
                    <ul>
                        {section name="id" loop="$articles"}
                            <li>
                                <a href="/feed/{$articles[id].id}-{$articles[id].title|transliterate|lower}">{$articles[id].title}</a>
                            </li>
                        {/section}
                    </ul>
                {/if}

            </div>
        </section>
    {/if}

    {include file="partials/catalog-list-options.tpl"}

    <div class="row mt-lg-5">
        <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3 sidebar-container">
            {include file="partials/catalog-sidebar.tpl"}
        </div>
        {include file="partials/catalog-items-cards.tpl" items=$items class="col-sm-12 col-md-8 col-lg-9 col-xl-9"}
    </div>
</div>

