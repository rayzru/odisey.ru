<div class="container">
	{if isset($error)}
	<h4>Ошибочный, отсутствующий или недоступный номер заказа</h4>
	{else}
		<h1 class="page-title">Детализация заказа &numero;{$order}
			<a class="btn btn-sm btn-outline-secondary"
			   style="letter-spacing: normal"
			   href="/my/orders">Заказы</a>
			<a class="btn btn-sm btn-outline-secondary"
			   style="letter-spacing: normal"
			   href="/my/orders/archive">Архив заказов</a>
		</h1>
		<div class="order">
			<table class="cart table table-hover">
				<thead>
				<th></th>
				<th>Товар</th>
				<th>Стоимость</th>
				<th>Количество</th>
				</thead>
				<tbody>
				{section name="id" loop="$items"}
					<tr>
						<td>
							<small class="text-muted">{$smarty.section.id.iteration}</small>
						</td>
						<td>
							<div><a href="/catalog/p{$items[id].item_id}-{$items[id].title|transliterate|mb_strtolower}">{$items[id].title}</a></div>
							<small class="text-muted">{$items[id].articul}</small>
						</td>
						<td>
							{if $items[id].price != "0.00"}
								{$items[id].price|string_format:"%d"}
								<i class="icon-rouble" style="font-size: 11px;" title="рублей"></i>
							{else}
								&mdash;
							{/if}
						</td>
						<td>
							{$items[id].quantity}
						</td>
						<td>
							{assign var="stock" value="`$items[id].stock`"}
							<div class="catalog-items__stock catalog-items__stock--{$stock}"
								 title="{$stocks[$stock].description}">
								{$stocks[$stock].title}
							</div>
						</td>
					</tr>
				{/section}
				</tbody>
			</table>
			{if ($status.priceempty)}
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					{$warningsStrings.priceempty}
				</div>
			{/if}
			{if ($status.stockorder)}
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					{$warningsStrings.stockorder}
				</div>
			{/if}
			{if ($status.stocknone)}
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					{$warningsStrings.stocknone}
				</div>
			{/if}
		</div>
	{/if}
</div>
