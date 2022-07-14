<div class="container">
	{include file="partials/breadcrumbs.tpl"}
	<h1 class="page-title">{$category.title}</h1>

	{if !empty($category.description)}
		<article class="pb-5">
			{$category.description}
		</article>
	{/if}
	<div class="row">
		{section name=i loop=$items}
			<div class="catalog-items col ">
				<div class="catalog-items__wrapper">
					<a class="catalog-items__image-wrapper"
					   rel="canonical"
					   href="/catalog/{$items[i].id}-{$items[i].title|transliterate|lower}">
						<div class="catalog-items__image grayscale"
							 style="background-image: url({if $items[i].filename}{$items[i].filename|mediacachepath:'200x200'}{else}{'00-blank.jpg'|mediacachepath:'200x200'}{/if});"></div>
					</a>
					<h3 class="card-title catalog-items__title">
						<a rel="canonical"
						   href="/catalog/{$items[i].id}-{$items[i].title|transliterate|lower}">{$items[i].title} <small class="catalog-items__items-count">({$items[i].items_count})</small></a>
					</h3>
				</div>
			</div>
		{/section}
	</div>
</div>
