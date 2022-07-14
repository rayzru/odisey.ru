function affixMainMenu() {
	var top = $(window).scrollTop();
	var menu = $('#mainmenu').outerHeight();
	if (top > 0) {
		$('.interact-box__wrapper')
			.addClass('affix')
			.css('top', (top + menu) + 'px');
	} else {
		$('.interact-box__wrapper')
			.removeClass('affix');
	}
}

function add2cart(button, id) {
	var btn = $(button);
	btn.prop('disabled', true);
	$.post("/my/cart/add", {id: id}) .done(function(data) {
		if (data == 'error') {
			btn.popover({'content': 'Ошибка добавления товара в корзину'});
		} else {
			btn.popover({
				'placement': 'bottom',
				'content': '<h5>Добавлено</h5>' +
					'Товар добавлен в корзину.<br/>Что бы ознакомится с товарами в вашей корзине,' +
					'<br/>перейдите в <a href=\'/my/\'>личный кабинет</a>.',
				'html': true
			});
			if (!btn.hasClass('active')) {
				btn.text('В корзине').addClass('active');
			}
			btn.popover('show');
			setTimeout(function () {
				btn.popover('dispose');
			}, 3000);
		}
	}).always(function () {
		btn.prop('disabled', false);
		updateCart();
	});
}

function setCart(data) {
	if (data && data.count) {
		if ($('.cabinet-nav__count').length == 0) {
			$counter = $('<span>' + data.count + '</span>')
				.addClass('cabinet-nav__count')
				.appendTo('.cabinet-nav__cart');
		} else {
			$('.cabinet-nav__count').text(data.count);
		}
		$('.cabinet-nav__cart')
			.addClass('cabinet-nav__item hasItems')
			.attr('title', data.count + ' ' + plural(data.count, 'товар', 'товара', 'товаров'));

		var c = $('.cabinet-nav__count:eq(0)')
			.clone()
			.addClass('cabinet-nav__effect')
			.appendTo('.cabinet-nav__cart');

		setTimeout(function () {
			c.addClass('cabinet-nav__effect--fade')
			setTimeout(function () {
				c.remove();
			}, 1000)
		}, 100)
	}
}

function updateCart() {
	$.get("/my/cart/json", function (data) {
		if (data) {
			setCart(data);
		}
	});
}

$(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();

	$(window).on('scroll', affixMainMenu);
	affixMainMenu();

	$('#mainmenu li.dropdown').hover(function () {
		$(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(200);
	}, function () {
		$(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(200);
	});
});

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
	scrollFunction()
};

function scrollFunction() {
	if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 20) {
		document.getElementById("toTopButton").style.display = "block";
	} else {
		document.getElementById("toTopButton").style.display = "none";
	}
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
	document.body.scrollTop = 0; // For Safari
	document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}