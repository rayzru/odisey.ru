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

	$('.select2.category').select2({
		minimumInputLength: 2,
		ajax: {
			url: "/admin/catalog/getLeafCategories",
			dataType: 'json'
		},
		processResults: function (data) {
			return {
				results: data.map(function (el) {
					return {text: el.text, id: el.id};
				})
			};
		}
	});

	$('.select2.feature').select2({
		minimumInputLength: 2,
		multiple: false,
		closeOnSelect: true,
		selectOnClose: true,
		ajax: {
			url: "/admin/catalog/getFeatures",
			dataType: 'json',
			processResults: function (data) {
				var inputs = $('#features .feature-value');
				return {
					results: data.map(function (el) {
						return {
							text: el.title + ((el.unit !== '') ? ' (' + el.unit + ')' : ''),
							id: el.id, title: el.title, unit: el.unit, feature_value: null,
							disabled: ($.inArray(el.id, inputs.map(function () {
								return $(this).data('feature');
							}).get()) !== -1)
						};
					})
				}
			}
		}
	});

	$('.select2.clone').select2({
		multiple: false,
		closeOnSelect: true,
		selectOnClose: false
	});

	$('.select2.articles').select2({
		minimumInputLength: 2,
		multiple: false,
		closeOnSelect: true,
		selectOnClose: true,
		ajax: {
			url: "/admin/catalog/getArticles",
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

	$('.select2.similar, .select2.related').select2({
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
});

