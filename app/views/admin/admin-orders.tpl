<div class="catalog-content__header">
	<h3>Заказы</h3>
	<div class="row">
		<div class="col">
			<form class="form-inline">
				<div class="form-group">
					<input type="hidden" name="page" value="{$orders.page}">
					<div class="btn-group btn-group-sm" data-toggle="buttons">
						{foreach from=$order_statuses key=k item=status}
							<label class="btn btn-outline-secondary {if isset($filter_statuses.$k)}active{/if}" >
								<input class="mr-sm-1" type="checkbox" name="status[]" value="{$k}" {if isset($filter_statuses.$k)}checked{/if}>
								{$status}
								<small class="ml-2">{$statuses_count.$k|default:0}</small>
							</label>
						{/foreach}
					</div>
					<div class="btn-group ml-3">
						<button type="submit" class="btn btn-info btn-sm">Фильтр</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col">
			<nav aria-label="">
				<ul class="pagination pagination-sm justify-content-end">
					{if $orders.pagerStart > 0}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
					{section name="page" start="`$orders.pagerStart`" loop="`$orders.pagerEnd`" }
						{assign var="si" value="`$smarty.section.page.index` + 1"}
						{math equation="$si" assign="i"}
						<li class="page-item {if $orders.page == $i}active{/if}">
							<a class="page-link" href="?page={$i}">{$i}</a>
						</li>
					{/section}
					{if $orders.pagerEnd < $orders.pages}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
				</ul>
			</nav>
		</div>
	</div>
</div>
<div class="catalog-content__data">
	<div class="container-fluid">
		<table class="table">
			<thead>
			<tr>
				<th title="Номер заказа">&numero;</th>
				<th title="Дата заказа">Дата</th>
				<th>Статус</th>
				<th>Товаров</th>
				<th>Пользователь</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name=id loop="`$orders.items`"}
				{assign var="oc" value="`$orders.items[id].status`"}
				<tr class="{$orders.items[id].status}" rel="{$orders.items[id].id}" data-id="{$orders.items[id].id}">
					<td><a href="/admin/orders/{$orders.items[id].id}"
						   data-loading-text="<i class='fa fa-spin fa-refresh'></i>"
						   title="Детализация заказа">{$orders.items[id].id}</a></td>
					<td data-orderdate="{$orders.items[id].added}">{$orders.items[id].added|date_format:"%d-%m-%Y"}
						<small>{$orders.items[id].added|date_format:"%H:%M"}</small>
					</td>
					<td><span class="badge badge-{$order_labels.$oc}">{$order_statuses.$oc}</span></td>
					<td>
						<a href="/admin/orders/{$orders.items[id].id}"
						   class="btn btn-sm btn-outline-secondary btn-block"
						   title="Детализация заказа">{$orders.items[id].items_count} <i
									class="fa fa-list"></i></a>
					</td>
					<td>{$orders.items[id].email} {if $orders.items[id].identifier != ""}({$orders.items[id].identifier}){/if}</td>
					<td class="text-right">
						{if $orders.items[id].status == 'added'}
							<a href="/admin/orders/{$orders.items[id].id}/queued"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Подтвердить текущий заказ?');">Принять</a>
							<a href="/admin/orders/{$orders.items[id].id}/rejected"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Отменить текущий заказ?');">Отменить</a>
						{/if}

						{if $orders.items[id].status == 'rejected'}
							<a href="/admin/orders/{$orders.items[id].id}/added"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Заказ был отменен. Вы действительно хотите восстановить?');">Восстановить</a>
						{/if}

						{if $orders.items[id].status == 'queued'}
							<a href="/admin/orders/{$orders.items[id].id}/closed"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Все товары были отданы заказчику, пометить заказ как выполненый?');">Выполнен</a>
							<a href="/admin/orders/{$orders.items[id].id}/rejected"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Отменить текущий заказ?');">Отменить</a>
						{/if}
					</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>