<div class="import-items">
	<legend class="import-items__head" style="flex: 0;">Пакетные операции в выбранными позициями</legend>
	<div class="import-items__table">
		<table class="table" id="data">
			<thead>
			<th>Артикул</th>
			<th>Наименование товара</th>
			</thead>
			<tbody>
			{section name="id" loop="$items"}
				<tr>
					<td>{$items[id].articul}</td>
					<td>{$items[id].title}</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
	<div class="import-items__operation">
		<form method="post" class="form-inline" action="/admin/catalog/import/batch/process">
			{section name="id" loop="$items"}
				<input type="hidden" name="item[]" value="{$items[id].id}">
			{/section}
			<select class="form-control" name="operation" data-placeholder="Выберите операцию">
				<option value="noop">Операция не выбрана</option>
				<optgroup label="Наличие">
					<option value="stock_stock">Пометить позиции в наличии</option>
					<option value="stock_order">Пометить позиции в заказе</option>
					<option value="stock_none">Пометить временно отсутствующие позиции</option>
				</optgroup>
				<optgroup label="Видимость позиций">
					<option value="visibility_1">Показывать позиции</option>
					<option value="visibility_0">Скрыть позиции</option>
				</optgroup>
				<optgroup label="Предупреждение о розничной цене">
					<option value="price_warn_1">Показывать предупреждение</option>
					<option value="price_warn_0">Не показывать предупреждение</option>
				</optgroup>
				<optgroup label="Другие флаги">
					<option value="new_1">новинка - установить</option>
					<option value="new_0">новинка - снять</option>
					<option value="special_1">акция - установить</option>
					<option value="special_0">акция - снять</option>
					<option value="top_1">лидер продаж - установить</option>
					<option value="top_0">лидер продаж - снять</option>
					<option value="commission_1">комиссионный - установить</option>
					<option value="commission_0">комиссионный - снять</option>
				</optgroup>
				<optgroup label="Удаление">
					<option value="delete">Удалить перечень позиций</option>
				</optgroup>
			</select>
			<button class="btn btn-primary">Выполнить</button>
		</form>
	</div>
</div>
