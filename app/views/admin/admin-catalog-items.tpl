<table class="table table-striped" id="itemsTable">
	<thead>
	<tr>
		<th></th>
		<th title="Активность товара"><i class="fa fa-check-circle-o"></i></th>
		<th>Товар</th>
		<th>Артикул</th>
		<th title="Флаги"><i class="fa fa-tag"></i></th>
		<th class="text-right">Цена</th>
		<th>Наличие</th>
		<th title="Изображения"><i class="fa fa-photo"></i></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	{section name="id" loop="$items"}
		<tr class="" id="row-{$items[id].id}" rel="{$items[id].id}">
			<td class="dragCursor"><i class="fa fa-arrows-v"></i></td>
			<td>{if $items[id].flag_active === 1}<i class="fa fa-check-circle"></i>{/if}</td>
			<td>
				<a href="/admin/catalog/p{$items[id].id}/">{$items[id].title}</a><br/>
			</td>
			<td>
				<small class="text-muted small">{$items[id].articul}</small>
			</td>
			<td>
				{if $items[id].flag_new === 1}<span class="badge badge-default">Новый</span>{/if}
				{if $items[id].flag_special === 1}<span class="badge badge-default">Акция</span>{/if}
				{if $items[id].flag_top === 1}<span class="badge badge-default">Лучший</span>{/if}
				{if $items[id].flag_commission === 1}<span class="badge badge-default">Комиссионный</span>{/if}
			</td>
			<td class="text-right">
				{if $items[id].flag_price_warn === 1}
					<i title="Предупреждение о розничной цене" class="fa fa-bullhorn text-warning"></i>
				{/if}
			{if $items[id].price != 0}
				{$items[id].price|number_format:2:".":""|replace:".00":''}
				<i class="text-muted small fa fa-rub"></i>
			{/if}
			</td>
			<td>
				{assign var="stock" value="`$items[id].stock`"}
				<div class="catalog-items__stock catalog-items__stock--{$stock}"
					 title="{$stocks[$stock].description}">
					{$stocks[$stock].title}
				</div>
			</td>
			<td>{if !empty($items[id].filename)}<i class="fa fa-check-circle"></i>{else}{/if}</td>
			<td>
			<a href="#"
				class="btn btn-danger btn-sm"
				onclick="deleteItem({$items[id].id});"><i class="fa fa-times"></i></a>
			</td>
		</tr>
	{/section}
	</tbody>
</table>
{literal}
<script>
	function deleteItem(item_id) {
		if (confirm('Подтвердите удаление позиции?')) {
			$.ajax({
				url: '/admin/catalog/item/' + item_id,
				type: 'DELETE',
				success: function () {
					$('tr#row-' + item_id).remove();
				}
			});
		}
	}

	jQuery(function ($) {
		let fixHelperModified = function(e, tr) {
    		let $originals = tr.children();
    		let $helper = tr.clone();
			$helper.children().each(function(index) {
				$(this).width($originals.eq(index).width())
			});
			return $helper;
		},

	    updateIndex = function(e, ui) {
			const s = [];
			$('tr', ui.item.parent()).each(function (i, el) { s.push($(el).attr('rel'));});
			resortItems({/literal}{$category.id}{literal}, s);
    	};

		$("#itemsTable tbody").sortable({ helper: fixHelperModified, stop: updateIndex }).disableSelection();

	});

</script>
{/literal}