<div class="items-container {$class}">
	<div class="row">
		{section name=i loop=$items}
			<meta itemprop="position" content="{$smarty.section.i.iteration}"/>
			<div class="col catalog-items"
				 data-price="{$items[i].price}"
				 data-stock="{$items[i].stock}"
				 itemscope
				 itemprop="itemListElement"
				 itemtype="http://schema.org/ListItem"
				 rel="{$items[i].id}">

				{if !$noselect}
				<label class="catalog-items__select">
					<input class="catalog-items__select-input"
						   type="checkbox"
						   value="{$items[i].id}"
						   name="selected[{$items[i].id}]">
					<div class="catalog-items__select-mark"></div>
				</label>
				{/if}

				<div class="catalog-items__wrapper"
					 itemprop="item"
					 itemscope
					 itemtype="http://schema.org/Product">
					<meta itemprop="sku" content="{$items[i].articul}"/>
					<div class="catalog-items__badges">
						{if $items[i].price > 0 && $items[i].stock != 'none'}
							<div class="catalog-items__badge catalog-items__price {if $items[i].stock == 'order'}warn{/if} {if $items[i].discount}discount{/if} {if $items[i].flag_commission == 1}catalog-items__price-commission{/if}"
								 title="{if $items[i].discount}Данный товар является акционным. На него распространяется скидка {$items[i].discount}{if $items[i].discount_unit === 'percent'}%{else}р.{/if}{elseif $items[i].flag_commission == 1}Товар является комисионным{/if}"
								 {if $items[i].stock == 'order'}
									 data-toggle="popover"
									 data-trigger="hover"
									 data-placement="left"
									 data-content="Товар доступен для приобретения под заказ. Цена может быть не актуальной. Для более точных данных обратитесь к нашим менеджерам."
									 data-original-title=""
								 {/if}
							>
								{if $items[i].discount}
									{if $items[i].discount_unit === 'percent'}
										{math assign="val" equation='round(x-((x/100)*y))' x=$items[i].price y=$items[i].discount}
									{else}
										{math assign="val" equation='x-y' x=$items[i].price y=$items[i].discount}
									{/if}
								{else}
									{assign var="val" value="`$items[i].price`"}
								{/if}
								{$val|number_format:2:".":""|replace:".00":''}

								<i class="icon-rouble"></i>

								<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                            <meta itemprop="priceCurrency" content="RUB"/>
                                            <meta itemprop="price" content="{$items[i].price|number_format:2:".":""|replace:".00":''}"/>
                                            <meta itemprop="url" content="https://odisey.ru/catalog/p{$items[i].id}-{$items[i].title|transliterate|lower}"/>
                                            {if $items[i].stock == 'stock'}<meta itemprop="availability" content="https://schema.org/InStock"/>
											{elseif $items[i].stock == 'order'}<meta itemprop="availability" content="https://schema.org/PreOrder"/>
											{elseif $items[i].stock == 'none'}<meta itemprop="availability" content="https://schema.org/OutOfStock"/>
											{/if}
                                        </span>
							</div>
							{if $items[i].discount}
								<div class="catalog-items__badge catalog-items__discounted-price">
									{$items[i].price|number_format:2:".":""|replace:".00":''}
									<i class="icon-rouble"></i>
								</div>
							{/if}
						{/if}

						{if $items[i].flag_new == 1}
							<div class="catalog-items__badge catalog-items__new">Новинка</div>
						{/if}

						{if ($items[i].flag_special == 1 || $items[i].discount) && !$promo}
							{if $items[i].discount}
								<a href="/promo/{$items[i].promo_id}-{$items[i].promo_slug}" class="catalog-items__badge catalog-items__special">Акция</a>
							{else}
								<div class="catalog-items__badge catalog-items__special">Акция</div>
							{/if}

						{/if}

						{if $items[i].flag_top == 1}
							<div class="catalog-items__badge catalog-items__top"
								 title="Данная позиция популярная среди наших покупателей">Популярый
							</div>
						{/if}
					</div>

					<a class="catalog-items__image-wrapper"
					   rel="canonical"
					   href="/catalog/p{$items[i].id}-{$items[i].title|transliterate|lower}">
						<div class="catalog-items__image"
							 style="background-image: url({if $items[i].filename}{$items[i].filename|mediacachepath:'200x200'}{else}{'00-blank.jpg'|mediacachepath:'200x200'}{/if});"></div>
					</a>
					{if $items[i].filename}
						<meta itemprop="image" content="{$items[i].filename|mediacachepath:'500x500'}" />
					{/if}
					<div class="catalog-items__stats">
						<small title="Голосов {$items[i].votes}"
							   class="catalog-items__rating {if $items[i].rating != 0}catalog-items__rating--active{/if}">
							{section name=j loop=6 start=1 max=6}<i
								class="icon-star{if $smarty.section.j.index > $items[i].rating }-empty{/if}"></i>
							{/section}
							{if $items[i].votes > 0}
								<span itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating"
									  itemscope>
                                            <meta itemprop="ratingValue" content="{$items[i].rating}"/>
                                            <meta itemprop="reviewCount" content="{$items[i].votes}"/>
                                        </span>
							{/if}
							<span class="catalog-items__votes">{$items[i].votes}</span>
						</small>
						{assign var="stock" value="`$items[i].stock`"}
						<div class="catalog-items__stock catalog-items__stock--{$stock}"
							 title="{$stocks[$stock].description}">
							{$stocks[$stock].title}
						</div>
					</div>

					<h3 class="card-title catalog-items__title">
						<a itemprop="url"
						   href="/catalog/p{$items[i].id}-{$items[i].title|transliterate|lower}">
							<span itemprop="name">{$items[i].title}</span>
						</a>
					</h3>

					<div class="catalog-items__action">
						<small class="catalog-items__key" itemprop="sku">{$items[i].articul}</small>
						<a href="#cart"
						   onclick="add2cart(this, {$items[i].id});"
						   class="catalog-items__cart-button d-print-none {if $items[i].in_cart}active{/if}">В
							корзин{if $items[i].in_cart}е{else}у{/if}</a>
					</div>

					{if !empty($items[i].features)}
						<div class="catalog-items__features">
							{section name=f loop="`$items[i].features`"}
								<dl>
									<dt>{$items[i].features[f].feature_title}</dt>
									<dd>{$items[i].features[f].feature_value} {$items[i].features[f].feature_unit}</dd>
								</dl>
							{/section}
						</div>
					{/if}
				</div>
			</div>
		{/section}
	</div>
</div>