<div class="container ">
	{include file="partials/breadcrumbs.tpl"}
	<h1 class="page-title">{$promo.title}</h1>

	<div class="promo">
		<article class="promo__description">
			{$promo.description}
		</article>
		{if count($promo.items)}
		<h3 class="promo__subtitle">Товары, которые учавствуют в акции</h3>
		<div class="promo__items">
			{include file="partials/catalog-items-cards.tpl" items=$promo.items noselect="true"}
		</div>
		{/if}
	</div>
</div>
