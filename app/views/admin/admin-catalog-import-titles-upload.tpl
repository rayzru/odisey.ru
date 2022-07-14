<div class="import-items">
	<legend class="import-items__head" style="flex: 0;">Изменение наименований</legend>
	<div class="import-items__table">
		<table class="table" id="data">
			<thead>
			<th>Артикул</th>
			<th>Наименование товара</th>
			<th>Новое наименование товара</th>
			</thead>
			<tbody>
			{section name="id" loop="$items"}
				<tr>
					<td>{$items[id].articul}</td>
					<td>{$items[id].title}</td>
					<td>{$items[id].new_title}</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
	<div class="import-items__operation">
		<form method="post" action="/admin/catalog/import/titles/process">
			{section name="id" loop="$items"}
				<input type="hidden" name="title[{$items[id].id}]" value="{$items[id].new_title|escape}">
			{/section}
			<button class="btn btn-primary">Сохранить</button>
		</form>
	</div>
</div>