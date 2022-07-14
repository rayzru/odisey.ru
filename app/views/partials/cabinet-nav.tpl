<div class="cabinet-nav">
    <a href="/my/cart"
       class="cabinet-nav__cart cabinet-nav__item {if ($cartCount > 0)}hasItems{/if}"
       title="{if ($cartCount > 0)}{$cartCount} {$cartCount|plural:"товар":"товара":"товаров"}{else}Корзина{/if}">
        <i class="icon-basket"></i>
        {if ($cartCount > 0)}<span class="cabinet-nav__count">{$cartCount}</span>{/if}
    </a>
    <div class="cabinet-nav__user cabinet-nav__item {if $account->isLogged()}logged{/if}">
        <a class="cabinet-nav__username"
           title="{$account->email}"
           href="/my">
            <i class="icon-user"></i>
            <span class="cabinet-nav__username-text d-lg-inline d-none">
			{if $account->isLogged()}
                {$account->email}
            {else}
                Вход
            {/if}
			</span>
        </a>
        {if $account->isLogged()}
            <div class="cabinet-nav__user-menu">
                <a href="/my/cart">Корзина</a>
                <a href="/my/orders">Заказы</a>
                <a href="/my/profile">Профиль</a>
                <a href="/logout">Выйти</a>
            </div>
        {else}
            <div class="cabinet-nav__user-menu">
                <div class="p-3">{include file="partials/my-auth-form.tpl"}</div>
            </div>
        {/if}
    </div>
</div>

<script>{literal}
	jQuery(function ($) {
		$('#navbarService').on('mouseover', () => {
			$('#navbarService').parent().addClass('active');
		});
	});
    {/literal}
</script>
