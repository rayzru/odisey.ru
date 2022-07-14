<script type="application/ld+json">
{strip}{literal}{{/literal}
	"@context": "http://schema.org/",
	"@type": "Product",
	{if ($item.votes > 0)}
	"aggregateRating": {literal}{{/literal}
		"@type": "AggregateRating",
		"ratingValue": "{$item.rating}",
		"reviewCount": "{$item.votes}"
	{literal}}{/literal},{/if}
	"name": "{$item.title|escape}",
	{if count($item.images)}"image": "{$item.images[0].filename|mediacachepath:''}",{/if}
	"description": "{strip}{$item.seo_description}{/strip}",
	"productID": "sku:{$item.articul}"{if ($item.price > 0)},
	"offers": {literal}{{/literal}
		"@type": "Offer",
		"priceCurrency": "RUB",
		"price": "{$item.price}",
		"itemCondition": "new"
	{literal}}{/literal}
	{/if}
{literal}}{/literal}
{/strip}
</script>