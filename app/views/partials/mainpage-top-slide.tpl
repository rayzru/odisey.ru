{section name=i loop=$el}
	<div>
		<a href="/catalog/viewItem/{$el[i].item_id}"
		   class="item"
		   title="{$el[i].item_title|escape}">

			{if $el[i].show_image == 'true'}
				<img class="lazyOwl item__image"
					 alt="{$el[i].item_title|escape}"
					 src="/assets/images/catalog/250x250/00-blank.jpg"
					 data-src="/assets/images/catalog/250x250/{if $el[i].image_file != ''}{$el[i].image_file}{else}00-blank.jpg{/if}"/>
			{/if}
			<div class="item__info">
				{if $el[i].price > 0 && $el[i].availability != 2}
					<small class="item__price">
						{$el[i].price|number_format:2:".":""|replace:".00":''}
						<i class="icon-rouble"></i>
					</small>
				{/if}
				<h3 class="item__title">{$el[i].item_title}</h3>
			</div>
		</a>
	</div>
{/section}
