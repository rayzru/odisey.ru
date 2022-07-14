<div class="container">
	{include file="partials/breadcrumbs.tpl"}
	<h1 class="page-title">Кабинет</h1>

	<div class="row">
		<div class="col col-lg-4">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Корзина</h4>
					<p class="card-text">
						Выбранные товары из <a href="/catalog/">каталога</a> для возможности формирования <a href="/my/orders">онлайн-заказа</a>.
					</p>
					<a href="/my/cart" class="btn btn-primary">{$cartCount}&nbsp;{$cartCount|plural:"товар":"товара":"товаров"}</a>
					<a href="/my/cart/clear" class="btn btn-warning">Очистить</a>
				</div>
			</div>
		</div>
		<div class="col col-lg-4">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Заказы</h4>
					<p class="card-text">
						Перечень сделанных Вами электронных заказов сделанных на нашем сайте.
					</p>
					<a href="/my/orders" class="btn btn-primary">Текущие заказы</a>
					<a href="/my/orders/archive" class="btn btn-secondary">Архив</a>
				</div>
			</div>
		</div>
		<div class="col d-none d-lg-block">
			<h4>Обратная связь</h4>
			<p>Если у вас возникли вопросы по заказу и оформлению товаров, доставке, наличию и сроках поставки, свяжитесь с нашими <a href="/contacts">менеджерами</a>.</p>

			<p>По вопросам монтажа, демонтажа, ремонта, гарантийного обслуживания вам следует обращатся в <a href="/service">сервисный центр компании</a>.</p>

		</div>
	</div>
</div>

