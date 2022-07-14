<div class="container">
	{include file="partials/breadcrumbs.tpl"}
	<h1 class="page-title">{$category.title}</h1>
	{include file="partials/catalog-list-options.tpl"}

	<div class="row mt-lg-5">
		<div class="col-9">
			<table class="table">
				<tbody>
				{section name=i loop=$items}
					<tr class="item{$items[i].item_id} catalog-list-items"
						rel="{$items[i].id}"
						data-item="{$items[i].item_id}"
						data-price="{$items[i].price}"
						data-stock="{$items[i].stock}"
					>
						<td class="pl-0">
							<a itemprop="url" href="/catalog/p{$items[i].id}-{$items[i].title|transliterate|lower}">
								{$items[i].title}</a>
							<div class="catalog-list-items__stats">
								<small class="catalog-list-items__key">{$items[i].articul}</small>
								<small title="Голосов {$items[i].votes}"
									   class="catalog-list-items__rating {if $items[i].rating != 0}catalog-list-items__rating--active{/if}">
									{section name=j loop=6 start=1 max=6}<i
										class="icon-star{if $smarty.section.j.index > $items[i].rating }-empty{/if}"></i>
									{/section}

									<span class="catalog-list-items__votes">{$items[i].votes}</span>
									{if $items[i].votes > 0}
										<span itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating"
											  itemscope>
                                            <meta itemprop="ratingValue" content="{$items[i].rating}"/>
                                            <meta itemprop="reviewCount" content="{$items[i].votes}"/>
                                        </span>
									{/if}
								</small>
								{assign var="stock" value="`$items[i].stock`"}
								<span class="catalog-list-items__stock catalog-list-items__stock--{$stock}"
									  title="{$stocks[$stock].description}">
										{$stocks[$stock].title}
									</span>
							</div>
						</td>
						<td class="text-right"  itemprop="offers" itemscope itemtype="http://schema.org/Offer">
							{if $items[i].price > 0 && $items[i].stock != 'none'}
							<span class="catalog-list-items__price">
								<span itemprop="price">{$items[i].price|number_format:2:".":""|replace:".00":''}</span>&nbsp;<i class="icon-rouble"></i>
							</span>
							<meta itemprop="priceCurrency" content="RUB"/>
							{/if}
							{if $items[i].stock == 'stock'}
								<meta itemprop="availability" content="https://schema.org/InStock"/>
							{elseif $items[i].stock == 'order'}
								<meta itemprop="availability" content="https://schema.org/PreOrder"/>
							{elseif $items[i].stock == 'none'}
								<meta itemprop="availability" content="https://schema.org/OutOfStock"/>
							{/if}
						</td>
						<td class="text-right pr-0">
							<button class="catalog-list-items__cart-button d-print-none" onclick="add2cart(this, {$items[i].id});">
								В корзину
							</button>
						</td>
					</tr>
				{/section}
				</tbody>
				<tfoot></tfoot>
			</table>
		</div>

		<div class="col col-3 sidebar-container">
			{include file="partials/catalog-sidebar.tpl"}
		</div>
	</div>
</div>
