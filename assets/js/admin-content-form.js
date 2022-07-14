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

	$('.select2.categories').select2({
		minimumInputLength: 2,
		multiple: true,
		tags: false,
		ajax: {
			url: "/admin/catalog/getCategories",
			dataType: 'json'
		}
	});
});