jQuery(function ($) {
	$('#createOrder').on('click', function () {
		if (confirm('Подтвердите создание заказа из перечня товаров в корзине')) {
			location.replace('/my/cart/order');
		}
	});
	$('.btn-remove-cart-item').on('click', function () {
		if (confirm('Подтвердите удаление выбранного товара из корзины')) {
			var btn = $(this);
			var id = btn.attr('rel');
			$.post("/my/cart/remove", {id: id}, function (data) {
				if (data == 'error') {
					btn.popover({'content': 'Ошибка удаления товара из корзины'});
				} else {
					location.reload();
				}
			});
		}
	});

	$('.passwordToggler').on('click', function (e) {
		e.preventDefault();
		$('#passwordForm, #passwordLink').toggle();
	});
});