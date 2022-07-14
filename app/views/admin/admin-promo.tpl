<div class="catalog-content__header">
	<h3>Акции</h3>
	<a href="/admin/promo/add" class="btn btn-primary btn-sm">Новая акция</a>
</div>
<div class="catalog-content__data">
	<div class="container-fluid">
		<table class="table" id="promo">
			<thead>
			<tr>
				<th width="100">Начало</th>
				<th width="100">Конец</th>
				<th>Акция</th>
				<th>Товаров</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name=id loop="$promo"}
				<tr class="{if $promo[id].active == 0}table-secondary{elseif $promo[id].current == 1}table-success{/if} " rel="{$promo[id].id}" data-id="{$promo[id].id}">
					<td>{$promo[id].date_start|date_format:"%d.%m.%Y"}</td>
					<td>{$promo[id].date_end|date_format:"%d.%m.%Y"}</td>
					<td><a href="/admin/promo/{$promo[id].id}">{$promo[id].title}</a></td>
					<td><a href="/admin/promo/{$promo[id].id}">{$promo[id].items_count}</a></td>
					<td class="text-right">
						<a href="/admin/promo/{$promo[id].id}/json"
						   class="btn btn-sm btn-outline-secondary delete"
						   onclick="return confirm('Удалить акцию безвозвратно?');">Удалить</a>
					</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>