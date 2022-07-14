<div class="page-navigation">
	<nav class="nav flex-column">
		<a class="nav-link {if (!$controller)}active{/if}" href="/admin" title="Главная" data-toggle="tooltip"
		   data-placement="right">
			<i class="fa fa-tachometer-alt"></i>
		</a>
		<a class="nav-link {if ($controller === 'catalog')}active{/if}" title="Каталог" href="/admin/catalog"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-sitemap"></i>
		</a>
		<a class="nav-link {if ($controller === 'orders')}active{/if}" title="Заказы" href="/admin/orders"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-shopping-cart"></i>
			<span class="badge badge-primary badge-pill">{$ordersCount}</span>
		</a>
		<a class="nav-link {if ($controller === 'callbacks')}active{/if}" title="Обратные звонки" href="/admin/callbacks"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-phone"></i>
			<span class="badge badge-primary badge-pill">{$callbacksCount}</span>
		</a>
		<a class="nav-link {if ($controller === 'reviews')}active{/if}" title="Отзывы" href="/admin/reviews"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-book-open"></i>
			<span class="badge badge-primary badge-pill">{$reviewsCount}</span>
		</a>
		<a class="nav-link {if ($controller === 'promo')}active{/if}" title="Акции" href="/admin/promo"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-certificate"></i>
			<span class="badge badge-primary badge-pill">{$promoCount}</span>
		</a>
		<a class="nav-link {if ($controller === 'content')}active{/if}" title="Новости и статьи" href="/admin/content"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-newspaper"></i>
		</a>
		<a class="nav-link {if ($controller === 'users')}active{/if}" title="Пользователи" href="/admin/users"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-users"></i>
		</a>
		<a class="nav-link {if ($controller === 'features')}active{/if}" href="/admin/features"
		   title="Справочник характеристик товаров" data-toggle="tooltip" data-placement="right">
			<i class="fa fa-list"></i>
		</a>
		<a class="nav-link {if ($controller === 'service')}active{/if}" href="/admin/service"
		   title="Обслуживание" data-toggle="tooltip" data-placement="right">
			<i class="fa fa-cog"></i>
		</a>

		<a class="nav-link end" href="/" title="Открыть сайт" target="_blank" title="Открыть сайт в новой вкладке"
		   data-toggle="tooltip" data-placement="right">
			<i class="fa fa-link"></i>
		</a>

		<a class="nav-link" href="?logout" title="Выйти" data-toggle="tooltip" data-placement="right">
			<i class="fa fa-power-off"></i>
		</a>
	</nav>
</div>
