<div class="import-items">
	<legend class="import-items__head" style="flex: 0;">Коррекция цен</legend>
	<div class="import-items__table">
		<table class="table" id="data">
			<thead>
			<th>Артикул</th>
			<th>Наименование товара</th>
			<th>Новая цена</th>
			</thead>
			<tbody>
			{section name="id" loop="$items"}
				<tr>
					<td>{$items[id].articul}</td>
					<td>{$items[id].title}</td>
					<td class="text-right">{$items[id].price|number_format:2:".":""|replace:".00":''} <i
								class="icon-rouble"></i></td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
	<div class="import-items__operation">
		<form method="post" action="/admin/catalog/import/prices/process">
			{section name="id" loop="$items"}
				<input type="hidden" name="price[{$items[id].id}]" value="{$items[id].price}">
			{/section}
			<button class="btn btn-primary">Сохранить</button>
		</form>
	</div>
</div>