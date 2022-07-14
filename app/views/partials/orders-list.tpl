<div class="orders">
	{section name="id" loop="`$orders.items`"}
		<div class="row orders__item">
			<div class="col">
				<a href="/my/orders/{$orders.items[id].id}/">Заказ #{$orders.items[id].id}</a>
				&mdash;
				<span class="text-muted">{$orders.items[id].added|carbondate:"%d.%m.%Y"}</span>
				<span class="text-muted" style="opacity: .4">{$orders.items[id].added|humandate}</span>
			</div>
			<div class="col text-right">
				{assign var="status" value="`$orders.items[id].status`"}
				<div class="badge badge-default">{$ordersStatuses.$status}</div>
			</div>
		</div>
	{/section}
</div>