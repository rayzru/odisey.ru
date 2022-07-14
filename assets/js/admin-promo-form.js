jQuery(function ($) {
	$('.select2.keywords').select2({
		minimumInputLength: 2,
		multiple: true,
		tags: true,
		ajax: {
			url: "/admin/catalog/getKeywords",
			dataType: 'json',
			processResults: function (data) {
				return {
					results: data.map(function (el) {
						return {text: el.keyword, id: el.id};
					})
				};
			}
		},
		createTag: function (params) {
			if (isFinite(params.term)) {
				return false;
			}
			return {
				id: params.term,
				text: params.term
			}
		}
	});

	$('.select2.items').select2({
		minimumInputLength: 2,
		multiple: false,
		closeOnSelect: true,
		selectOnClose: true,
		ajax: {
			url: "/admin/catalog/items/ajax",
			dataType: 'json',
			processResults: function (data) {
				return {
					results: data.map(function (el) {
						return {
							text: el.title,
							id: el.id,
							title: el.title
						};
					})
				}
			}
		}
	});

	function calcp(p, d) {
		return d < 100 ? Math.round(p - ((p / 100) * d)) : 0;
	}

	function calcr(p, d) {
		return p > d ? Math.round(p - d) : 0;
	}

	$('.item-add').on('click', function (e) {
		e.preventDefault();
		const item = {
			item_id: $('.select2.items').val(),
			promo_id: $('#promo_id').val(),
			discount: $('#discount').val(),
			discount_unit: $('#discount_unit').val(),
		};

		if (item.item_id && item.promo_id && item.discount && item.discount_unit) {
			$.post('/admin/promo/addItem', item, function (res) {
				if (res && res.success) {
					const aRow = '<tr data-item="' + res.item.id + '" id="item' + res.item.id + '">' +
						'<td>' + res.item.title + '</td>' +
						'<td>' + item.discount + (item.discount_unit === 'percent' ? '%' : 'руб') + '</td>' +
						'<td><span class="price-discounted">' +
						(res.item.price ? res.item.price + 'p.' : '') +
						'</span></td>' +
						'<td><span class="price-new">' +
						(res.item.price
							? (item.discount_unit === 'percent'
								? calcp(res.item.price, item.discount) + 'p.'
								: calcr(res.item.price, item.discount) + 'p.')
							: '') +
						'</span></td>' +
						'<td class="text-right" width="30"><button type="button" class="btn btn-danger btn-sm" ' +
						'onclick="removePromoItem(' + item.promo_id + ',' + res.item.id + ');"><i class="fa fa-times"></i></button></td></tr>';
					$('#items tbody').append(aRow);
				}
			})
		}
	})
});

function removePromoItem(promo, item) {
	if (confirm('Удалить товар из акции?')) {
		$.ajax({
			url: '/admin/promo/' + promo + '/item/' + item,
			method: 'DELETE',
			success: function () {
				$('tr#item' + item).remove();
			}
		})
	}
}