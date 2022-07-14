<div class="items-options">
	<div class="items-options__sort">
		<span>Сортировать</span>
		<a rel="nofollow" href="?order=order" class="{if empty($itemsorder) || $itemsorder=='order'}active{/if}">в оригинальном порядке</a>
		<a rel="nofollow" href="?order={if $itemsorder=='stock' && $itemsorderdir!='-'}-{/if}stock" class="{if $itemsorder=='stock'}active{/if}">по наличию{if $itemsorder=='stock'}<i class="icon icon-sort-{if $itemsorderdir!='-'}up{else}down{/if}"></i>{/if}</a>
		<a rel="nofollow" href="?order={if $itemsorder=='price' && $itemsorderdir!='-'}-{/if}price" class="{if $itemsorder=='price'}active{/if}">по цене{if $itemsorder=='price'}<i class="icon icon-sort-{if $itemsorderdir!='-'}up{else}down{/if}"></i>{/if}</a>
		<a rel="nofollow" href="?order={if $itemsorder=='title' && $itemsorderdir!='-'}-{/if}title" class="{if $itemsorder=='title'}active{/if}">по наименованию{if $itemsorder=='title'}<i class="icon icon-sort-{if $itemsorderdir!='-'}up{else}down{/if}"></i>{/if}</a>
		<a rel="nofollow" href="?order={if $itemsorder=='rating' && $itemsorderdir!='-'}-{/if}rating" class="{if $itemsorder=='rating'}active{/if}">по рейтингу{if $itemsorder=='rating'}<i class="icon icon-sort-{if $itemsorderdir!='-'}up{else}down{/if}"></i>{/if}</a>
		<a rel="nofollow" href="?order={if $itemsorder=='votes' && $itemsorderdir!='-'}-{/if}votes" class="{if $itemsorder=='votes'}active{/if}">по отзывам{if $itemsorder=='votes'}<i class="icon icon-sort-{if $itemsorderdir!='-'}up{else}down{/if}"></i>{/if}</a>
	</div>
</div>