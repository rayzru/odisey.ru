<div class="catalog-content__header">
	<h3>Заказ {$order.id} от {$order.added|format_date:"%d %B %Y, %H:%M"} <small>Заказчик: <a href="/users/{$order.user_id}">{$order.email}</a></small></h3>
	{include file="partials/admin-breadcrumbs.tpl"}

	<a href="/admin/orders"
	   class="btn btn-sm btn-outline-info">Перечень заказов</a>

	{if $order.status == 'added'}
		<a href="/admin/orders/{$order.id}/queued"
		   class="btn btn-sm btn-outline-success set-status"
		   onclick="return confirm('Подтвердить текущий заказ?');">Принять заказ</a>
		<a href="/admin/orders/{$order.id}/rejected"
		   class="btn btn-sm btn-outline-warning set-status"
		   onclick="return confirm('Отменить текущий заказ?');">Отменить заказ</a>
	{/if}

	{if $order.status == 'rejected'}
		<a href="/admin/orders/{$order.id}/added"
		   class="btn btn-sm btn-outline-primary set-status"
		   onclick="return confirm('Заказ был отменен. Вы действительно хотите восстановить?');">Восстановить отмененный заказ</a>
	{/if}

	{if $order.status == 'queued'}
		<a href="/admin/orders/{$order.id}/closed"
		   class="btn btn-sm btn-outline-secondary set-status"
		   onclick="return confirm('Все товары были отданы заказчику, пометить заказ как выполненый?');">Завершить заказ</a>
		<a href="/admin/orders/{$order.id}/rejected"
		   class="btn btn-sm btn-outline-warning set-status"
		   onclick="return confirm('Отменить текущий заказ?');">Отменить заказ</a>
	{/if}
</div>
<div class="catalog-content__data">
	<div class="container-fluid">
		<table class="table">
			<thead>
			<tr>
				<th title="Дата заказа"></th>
				<th>Товар</th>
				<th class="text-right">Количество</th>
				<th>Ед. изм.</th>
				<th>Артикул</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name=id loop="`$order.items`"}
				<tr>
					<td class="text-muted">{$smarty.section.id.index+1}</td>
					<td><a href="/admin/catalog/p{$order.items[id].item_id}">{$order.items[id].title}</a></td>
					<td class="text-right">{$order.items[id].quantity}</td>
					<td>{$order.items[id].unit}</td>
					<td>{$order.items[id].articul}</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>