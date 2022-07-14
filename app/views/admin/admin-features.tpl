<div class="catalog-content__header">
	<h3>Каталог характеристик</h3>
	<div class="row">
		<div class="col">
			<div class="form-inline">
				<form>
					<a href="/admin/features/add" class="btn btn-primary btn-sm">Новая характеристика</a>
					<input type="hidden" value="{$features.page}" name="page">
					<input type="text" value="{$features.query}" class="form-control form-control-sm" name="query">
					<button type="submit" class="btn btn-secondary btn-sm">Фильтр</button>
					{if (!empty($features.query))}<a href="/admin/features" class="btn btn-secondary btn-sm">Сбросить</a>{/if}
				</form>
			</div>
		</div>
		<div class="col">
			<nav aria-label="">
				<ul class="pagination pagination-sm justify-content-end">
					{if $features.pagerStart > 0}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
					{section name="page" start="`$features.pagerStart`" loop="`$features.pagerEnd`" }
						{assign var="si" value="`$smarty.section.page.index` + 1"}
						{math equation="$si" assign="i"}
						<li class="page-item {if $features.page == $i}active{/if}">
							<a class="page-link" href="?page={$i}">{$i}</a>
						</li>
					{/section}
					{if $features.pagerEnd < $features.pages}
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
				<th></th>
				<th>Наименование</th>
				<th>Единица</th>
				<th>Тип фильтра</th>
				<th>В разделах</th>
				<th>В товарах</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name="id" loop="`$features.items`"}
				<tr id="row-{$features.items[id].id}">
					<td class="text-muted">{$features.items[id].id}</td>
					<td><a href="/admin/features/{$features.items[id].id}/">{$features.items[id].title}</a></td>
					<td>{$features.items[id].unit}</td>
					<td>
						<small>
							{assign var="ftype" value="`$features.items[id].type`"}
							{if $ftype == ""}<span class="text-muted">не указан</span>{/if}
							{if $ftype == "range"}Диапазон значений{/if}
							{if $ftype == "single"}Одно из значений{/if}
							{if $ftype == "multiple"}Несколько значений{/if}
						</small>
					</td>
					<td>{$features.items[id].categories}</td>
					<td>{$features.items[id].items}</td>
					<td>
						<a href="/admin/features/{$features.items[id].id}/" class="btn btn-sm btn-outline-secondary"><i class="fa fa-edit"></i></a>
						{if $features.items[id].items == 0 && $features.items[id].categories == 0 }
							<a href="#"
							   onclick="deleteFeature({$features.items[id].id});return false;"
							   class="btn btn-sm btn-outline-danger"><i class="fa fa-times"></i></a>
						{/if}
					</td>
				</tr>
			{/section}
			</tbody>
		</table>

	</div>
</div>