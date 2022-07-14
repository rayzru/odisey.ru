{if !empty($reviews.items) && count($reviews.items)}
	<div class="catalog-item__reviews-list">
		{section name="id" loop="`$reviews.items`"}
			<div class="catalog-item__review" itemprop="review" itemscope itemtype="http://schema.org/Review">
				<meta itemprop="datePublished" content="{$reviews.items[id].updated|date_format:"%Y-%m-%d"}">
				<meta itemprop="worstRating" content="{$reviews.items[id].rating}">
				<div class="catalog-item__review__header">
					<small class="catalog-item__review__rating {if $item.rating != 0}catalog-item__review__rating--active{/if}"
						   title="{$reviews.items[id].rating}">
						{section name=j loop=6 start=1 max=6}
							<i class="icon-star{if $smarty.section.j.index > $reviews.items[id].rating }-empty{/if}"></i>
						{/section}
					</small>
					{if $reviews.items[id].anonymously == 1}
						<b class="catalog-item__review__user">{$reviews.items[id].email|mask_email:1:0 }</b>
					{else}
						<b class="catalog-item__review__user" itemprop="author">
							{$reviews.items[id].email}
							{if $reviews.items[id].identifier}, {$reviews.items[id].identifier}{/if}
						</b>
					{/if}
					<small class="catalog-item__review__date">
						{$reviews.items[id].updated|date_format:"%Y-%m-%d %H:%M"}
					</small>

				</div>
				{if !empty($reviews.items[id].review)}
				<div class="catalog-item__review__content" itemprop="description">
					{$reviews.items[id].review | strip_tags | nl2br }
				</div>
				{/if}
			</div>
		{/section}
	</div>
{/if}