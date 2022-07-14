jQuery(function ($) {
	$('.catalog-items__select-input').change(function (e) {
		if ($(this).prop('checked')) {
			$(this).parents('.catalog-items').addClass('selected');
		} else {
			$(this).parents('.catalog-items').removeClass('selected');
		}

		if ($('.catalog-items__select-input:checked').length > 0) {
			$('.catalog-sidebar__marked').show('fast');
		} else {
			$('.catalog-sidebar__marked').hide('fast');
		}
	});
});

function cartMarked(btn) {
	var items = [];
	$('.catalog-items__select-input:checked').each(function (index, el) {
		items.push($(el).val());
	});
	add2cart(btn, items);
	clearMarked();
}

function compareMarked() {
	var items = [];
	$('.catalog-items__select-input:checked').each(function (index, el) {
		items.push($(el).val());
	});
	document.location.href='/catalog/compare/' + items.join(',');
}

function clearMarked() {
	$('.catalog-items__select-input:checked').trigger('click');
}

/*var stickySidebar = $('.catalog-sidebar');

if (stickySidebar.length > 0) {
	var stickyHeight = stickySidebar.height(),
		sidebarTop = stickySidebar.offset().top - 150;
}

// on scroll move the sidebar
$(window).scroll(function () {
	if (stickySidebar.length > 0) {
		var scrollTop = $(window).scrollTop();

		if (sidebarTop < scrollTop) {
			stickySidebar.css('top', scrollTop - sidebarTop);

			// stop the sticky sidebar at the footer to avoid overlapping
			var sidebarBottom = stickySidebar.offset().top + stickyHeight,
				stickyStop = $('.sidebar-container').offset().top + $('.sidebar-container').height();
			if (stickyStop < sidebarBottom) {
				var stopPosition = $('.sidebar-container').height() - stickyHeight;
				stickySidebar.css('top', stopPosition);
			}
		}
		else {
			stickySidebar.css('top', '0');
		}
	}
});

$(window).resize(function () {
	if (stickySidebar.length > 0) {
		stickyHeight = stickySidebar.height();
	}
});

*/