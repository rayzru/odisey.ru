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

	$('.select2.features').select2({
		minimumInputLength: 2,
		multiple: true,
		ajax: {
			url: "/admin/catalog/getFeatures",
			dataType: 'json',
			processResults: function (data) {
				return {
					results: data.map(function (el) {
						return {
							text: el.title + ((el.unit !==  '') ? ' (' + el.unit + ')' : ''),
							id: el.id
						};
					})
				};
			}
		},
	});
});

function pickParentCategory(pid) {
	$('.select2.pcategory').select2({
		minimumInputLength: 2,
		ajax: {
			url: "/admin/catalog/pickParentCategory/" + pid,
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
}
