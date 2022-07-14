function plural_str(i, str1, str2, str3) {
	function plural(a) {
		if (a % 10 == 1 && a % 100 != 11) return 0;
		if (a % 10 >= 2 && a % 10 <= 4 && ( a % 100 < 10 || a % 100 >= 20)) return 1;
		return 2;
	}

	switch (plural(i)) {
		case 0: return str1;
		case 1: return str2;
		default: return str3;
	}
}

function showNotify(text, type) {
	//	bootstrapGrowl
	type = type || 'success';
	$.bootstrapGrowl(text, {
		type: type,
		align: 'center',
		width: 'auto',
		allow_dismiss: true
	});
}


function getParameterByName(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
	return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function add2cart(id) {
	//$('.add2CartButton' + id).
	var single = typeof id == 'number';

	if (single) {
		var el = $('.add2CartButton' + id);
		$(el).button('loading');
	}

	$.post("/my/add2CartAjax/", {id: id}, function (data) {
		if (data == 'error') {
			showNotify('Ошибка добавления товара в корзину', 'error');
		} else {
			//var num = 1;
			//showNotify(num + plural_str(num, ' товар добавлен',' товара добавлено',' товаров добавлено') + ' в корзину');
			if (single) {

			}
			showNotify("<h4>Добавлено</h4>Товар добавлен в корзину.<br/>Что бы ознакомится с товарами в вашей корзине,<br/>перейдите в <a href='/my/'>личный кабинет</a>.");
			updateCart();
		}
	}).always(function () {
		if (single) {
			$(el).button('reset');
		} else {
			$('.markedControls').animate({opacity: 0}, 200, function () {});
			$('.itemWrapper').removeClass('marked');
			$('.itemMarkCheckbox:checked').prop("checked", false);
		}
	});
}

function setCart(data) {
	if (data && data.count) {
        var num = data.count;
        $('#directCartLink').html('<i class="icon-basket"></i> ' + num + ' ' + plural_str(num, 'товар', 'товара', 'товаров'));
        $('.cartLink .label').text(num);
	}
}

function updateCart() {
	$.ajax({
		url: "/my/cart/json",
		type: "GET",
		dataType: "json",
		success: function (data) {
			if (data != 0) setCart(data);
		}
	});
}

$(function () {
	//updateCart();


	$('.itemData .thumbnail a').click(function () {
		$('#modalImage img').attr('src', $(this).attr('data-img-url'));
	});

	$('form.ajaxAuth').submit(function (event) {
		event.preventDefault();
		var form = this;
		$('.alert', form).remove();
		$('.ajaxAuthButton').button('loading');
		var data = $(form).serialize()
		$.post(form.action, data, function (response) {
			if (response != 'ok') {
				if ($(form).has('.errrorBox')) {
					$(form).prepend("<div class='alert alert-danger'><h4>Ошибка авторизации</h4>Введены неверные логин или пароль. Попробуйте еще раз.</div>");
				}
			} else {
				location.reload();
			}
			$('.ajaxAuthButton').button('reset');
		});
	});


});
