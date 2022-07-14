function dTokenizer(d) {
	var tokens = [];
	var stringSize = d.title.length;
	for (var size = 1; size <= stringSize; size++) {
		for (var i = 0; i + size <= stringSize; i++) {
			tokens.push(d.name.substr(i, size));
		}
	}
	return tokens;
}

$(function () {

	var searchItems = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			wildcard: '%QUERY',
			url: '/search/json/items/%QUERY'
		},
		limit: 7
	});

	searchItems.initialize();

	var searchCategories = new Bloodhound({
		datumTokenizer: dTokenizer,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			wildcard: '%QUERY',
			url: '/search/json/categories/%QUERY'
		},
		limit: 5
	});

	searchCategories.initialize();

	$('.typeahead').typeahead({
		minLength: 2,
		highlight: true
	}, {
		name: 'items-dataset',
		display: "title",
		source: searchItems.ttAdapter(),
		templates: {
			header: '<h3 class="tt-header">Товары</h3>',
			suggestion: function (data) {
				return '<p><a href="/catalog/' + data.slug + '">' + data.title +
					'</a><small>' + data.articul + '</small></p>';
			}
		}
	}, {
		name: 'categories-dataset',
		display: "title",
		source: searchCategories.ttAdapter(),
		templates: {
			header: '<h3 class="tt-header">Разделы</h3>',
			suggestion: function (data) {
				return '<p><a href="/catalog/' + data.slug + '">' + data.title + '</a></p>';
			}
		}
	});

	$('.typeahead').bind('typeahead:select', function (ev, suggestion) {
		console.log('Selection: ', suggestion);
		window.location.href = '/catalog/' + suggestion.slug;
	});
});
