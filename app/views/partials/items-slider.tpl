<div class="owl-carousel">
	{section name=i loop=$slides}
		<div class="card slider-product">

			<div class="slider-product__badges">
				{if $slides[i].flag_new == 1}<div class="slider-product__badge slider-product__badge--new">Новинка</div>{/if}
				{if $slides[i].flag_top == 1}<div class="slider-product__badge slider-product__badge--top">Лидер продаж</div>{/if}
				{if $slides[i].flag_special == 1}<div class="slider-product__badge slider-product__badge--special">Акция</div>{/if}
			</div>
			<a href="/catalog/p{$slides[i].id}-{$slides[i].title|transliterate|lower}"
			   title="{$slides[i].title|escape}"
			   class="slider-product__image-link">
				<img class="owl-lazy slider-product__image"
					 alt="{$slides[i].title|escape}"
					 src="{'00-blank.jpg'|mediacachepath:'150x150'}"
					 data-src="{if $slides[i].filename}{$slides[i].filename|mediacachepath:'150x150'}{else}{'00-blank.jpg'|mediacachepath:'150x150'}{/if}"/>
			</a>
			<div class="card-body">
				<h4 class="card-title slider-product__title">
					<a href="/catalog/p{$slides[i].id}-{$slides[i].title|transliterate|lower}"
					   title="{$slides[i].title|escape}">
						{$slides[i].title}
					</a>
				</h4>
				<p class="card-text">
					<small title="Голосов {$slides[i].votes}"
						   class="slider-product__rating {if $slides[i].rating != 0}slider-product__rating--active{/if}">
						{section name=j loop=6 start=1 max=6}<i
							class="icon-star{if $smarty.section.j.index > $slides[i].rating }-empty{/if}"></i>
						{/section}
						<span class="slider-product__votes">{$slides[i].votes}</span>

					</small>
				</p>
				{if $slides[i].price > 0 && $slides[i].availability != 2}
					<p class="card-text slider-product__price">
						{$slides[i].price|number_format:2:".":""|replace:".00":''}<i class="icon-rouble"></i>
					</p>
				{/if}
			</div>
		</div>
	{/section}
</div>
