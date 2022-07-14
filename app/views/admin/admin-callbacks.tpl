<div class="catalog-content__header">
	<h3>Обратные звонки</h3>
	<div class="row">
		<div class="col">
			<form class="form-inline">
				<div class="form-group">
					<input type="hidden" name="page" value="{$callbacks.page}">
					<div class="btn-group btn-group-sm" data-toggle="buttons">
						{foreach from=$callback_statuses key=k item=status}
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
					{if $callbacks.pagerStart > 0}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
					{section name="page" start="`$callbacks.pagerStart`" loop="`$callbacks.pagerEnd`" }
						{assign var="si" value="`$smarty.section.page.index` + 1"}
						{math equation="$si" assign="i"}
						<li class="page-item {if $callbacks.page == $i}active{/if}">
							<a class="page-link" href="?page={$i}">{$i}</a>
						</li>
					{/section}
					{if $callbacks.pagerEnd < $callbacks.pages}
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
				<th title="Дата заказа">Дата</th>
				<th>Статус</th>
				<th>Телефон</th>
				<th>Имя</th>
				<th>Комментарий</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name=id loop="`$callbacks.items`"}
				{assign var="oc" value="`$callbacks.items[id].status`"}
				<tr class="{$callbacks.items[id].status}" rel="{$callbacks.items[id].id}" data-id="{$callbacks.items[id].id}">
					<td data-orderdate="{$callbacks.items[id].added}">{$callbacks.items[id].added|date_format:"%d, %b"}
						<small>{$callbacks.items[id].added|date_format:"%H:%M:%S"}</small>
					</td>
					<td><span class="badge badge-{$callback_labels.$oc}">{$callback_statuses.$oc}</span></td>
					<td>
						{$callbacks.items[id].phone}
					</td>
					<td>{$callbacks.items[id].name}</td>
					<td>{$callbacks.items[id].comment}</td>
					<td class="text-right">
						{if $callbacks.items[id].status == 'new'}
							<a href="/admin/callbacks/{$callbacks.items[id].id}/accepted"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Обратный звонок был осуществлен?');">Подтвердить</a>
							<a href="/admin/callbacks/{$callbacks.items[id].id}/dismissed"
							   class="btn btn-sm btn-outline-secondary set-status"
							   onclick="return confirm('Обратный звонок отменяется?');">Отменить</a>
						{/if}
					</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>