<div class="container">

	<h1 class="page-title">Поиск &laquo;{$site->title}&raquo;</h1>
	{if $searchCategories}
	<div rel="categories" class="card search-categories">
		<div class="card-body">
			<h4 class="card-title">Вероятно, вы искали товары из разделов</h4>
			{section name=i loop=$searchCategories}
				<a class="search-categories__item" href="/catalog/{$searchCategories[i].slug}">{$searchCategories[i].title}</a>
			{/section}
		</div>
	</div>
	{/if}

	<span class="meta">{$searchItems|@count|plural:"Найден":"Найдено":"Найдено"} {$searchItems|@count} {$searchItems|@count|plural:"товар":"товара":"товаров"}</span>

	{if $searchItems}
	<div rel="items" class="search-items">
		{section name=i loop=$searchItems}
			<div class="search-items__item row">
				<div class="search-items__image img-thumbnail col col-1"
					 style="background-image: url({if $searchItems[i].filename}{$searchItems[i].filename|mediacachepath:'50x50'}{else}{'00-blank.jpg'|mediacachepath:'50x50'}{/if});"></div>
				<div class="search-items__info col">
					<a href="/catalog/{$searchItems[i].slug}"
					   title="{$searchItems[i].title | escape}"
					   class="search-items__title">{$searchItems[i].title}</a>
					<div class="search-items__meta">
						<span class="search-items__meta-key">{$searchItems[i].articul}</span>
						<small title="Голосов {$searchItems[i].votes}"
							   class="catalog-items__rating {if $searchItems[i].rating != 0}catalog-items__rating--active{/if}">
							{section name=j loop=6 start=1 max=6}<i
								class="icon-star{if $smarty.section.j.index > $searchItems[i].rating }-empty{/if}"></i>
							{/section}
							<span class="catalog-items__votes">{$searchItems[i].votes}</span>
						</small>
					</div>
				</div>
				<div class="search-items__cart col col-2">
					{if $searchItems[i].price > 0 && $searchItems[i].stock != 'none'}
						<div class="search-items__price">
							{$searchItems[i].price|number_format:2:".":""|replace:".00":''}
							<i class="icon-rouble"></i>
						</div>
					{/if}
				</div>
			</div>
		{/section}
	</div>
	{/if}
</div>
