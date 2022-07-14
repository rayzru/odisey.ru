<h2>Новый заказ #{$order.id}</h2>

<table class="cart table table-hover">
	<thead>
	<th></th>
	<th>Товар</th>
	<th>Стоимость</th>
	<th>Количество</th>
	</thead>
	<tbody>
	{assign var="items" value="`$order.items`"}
	{section name="id" loop="$items"}
		<tr>
			<td>
				<small class="text-muted">{$smarty.section.id.iteration}</small>
			</td>
			<td>
				<div><a href="{$emailTemplate.domain}/catalog/p{$items[id].item_id}-{$items[id].title|transliterate|mb_strtolower}">{$items[id].title}</a></div>
				<small class="text-muted">{$items[id].articul}</small>
			</td>
			<td>
				{if $items[id].price != "0.00"}
					{$items[id].price|string_format:"%d"}
					<i class="icon-rouble" style="font-size: 11px;" title="рублей"></i>
				{else}
					&mdash;
				{/if}
			</td>
			<td>
				{$items[id].quantity}
			</td>
			<td>
				{assign var="stock" value="`$items[id].stock`"}
				<div class="catalog-items__stock catalog-items__stock--{$stock}"
					 title="{$stocks[$stock].description}">
					{$stocks[$stock].title}
				</div>
			</td>
		</tr>
	{/section}
	</tbody>
</table>
