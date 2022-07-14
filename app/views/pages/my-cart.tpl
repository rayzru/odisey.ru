<div class="container">
	<h1 class="page-title">Корзина</h1>
	{if count($cart)}
		<form action="/my/cart/update" method="post">
			<table class="cart table table-hover">
				<thead>
				<th></th>
				<th>Товар</th>
				<th>Стоимость</th>
				<th>Количество</th>
				<th></th>
				<th></th>
				</thead>
				<tbody>
				{section name="id" loop="$cart"}
					<tr>
						<td>
							<small class="text-muted">{$smarty.section.id.iteration}</small>
						</td>
						<td>
							<div>
								<a href="/catalog/p{$cart[id].id}-{$cart[id].title|transliterate|mb_strtolower}">{$cart[id].title}</a>
							</div>
							<small class="text-muted">{$cart[id].articul}</small>
						</td>

						<td>
							{if $cart[id].price != "0.00"}
								{$cart[id].price|string_format:"%d"}
								<i class="icon-rouble" style="font-size: 11px;" title="рублей"></i>
							{else}
								&mdash;
							{/if}
						</td>
						<td>
							<input type="number"
								   value="{$cart[id].quantity}"
								   name="cart[{$cart[id].id}]"
								   class="form-control form-control-sm">
						</td>
						<td>
							{assign var="stock" value="`$cart[id].stock`"}
							<div class="catalog-items__stock catalog-items__stock--{$stock}"
								 title="{$stocks[$stock].description}">
								{$stocks[$stock].title}
							</div>
						</td>
						<td>
							<a href="#" title="Убрать товар из корзины"
							   class="btn btn-sm btn-outline-warning btn-remove-cart-item"
							   rel="{$cart[id].id}">&times;</a>
						</td>
					</tr>
				{/section}
				</tbody>
				<tfoot>
				<tr>
					<td colspan="6" class="text-right">
						<a href="/my/cart/clear" onclick="return confirm('Подтвердите очистку корзины');" class="btn btn-sm btn-outline-warning float-left">Очистить корзину</a>
						<button type="submit" class="btn btn-sm btn-outline-info">Пересчитать</button>
					</td>
				</tr>
				</tfoot>
			</table>
		</form>
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
		<div class="mb-5">
			{if $account->id}
				<a rel="nofollow" href="#" class="btn btn-lg btn-outline-success" id="createOrder">Оформить заказ</a>
			{else}
				<a href="/my/auth" class="btn btn-lg btn-outline-success">Войти</a>
				<span class="text-muted d-block mt-1">Для оформления заказа следует <a href="/my/register">зарегистрироваться</a> или пройти <a href="/my/auth">авторизацию</a>, если вы уже проходили процесс авторизации</span>
			{/if}
		</div>
	{else}
		<span class="text-muted">
			Корзина пуста, подберите что-нибудь в <a href="/catalog">каталоге товаров</a> или воспользуйтесь <a
					href="/search/">поиском</a>
		</span>
	{/if}
</div>

{literal}
	<script>

	</script>
{/literal}