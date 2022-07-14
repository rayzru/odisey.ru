$(function () {

	function itemsTokenizer(datum) {
		var titleTokens = Bloodhound.tokenizers.whitespace(datum.title);
		var articulTokens = Bloodhound.tokenizers.whitespace(datum.articul);
		return titleTokens.concat(articulTokens);
	}

	function categoryTokenizer(datum) {
		return Bloodhound.tokenizers.whitespace(datum.title);
	}

	var searchItems = new Bloodhound({
		datumTokenizer: itemsTokenizer,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			wildcard: '%QUERY',
			url: '/search/json/items/%QUERY'
		},
		limit: 10
	});

	searchItems.initialize();

	var searchCategories = new Bloodhound({
		datumTokenizer: categoryTokenizer,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			wildcard: '%QUERY',
			url: '/search/json/categories/%QUERY'
		},
		limit: 10
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
				return '<p><a href="/admin/catalog/p' + data.id + '">' + data.title +
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
				return '<p><a href="/admin/catalog/' + data.id + '">' + data.title + '</a></p>';
			}
		}
	});

	$('.typeahead').bind('typeahead:select', function (ev, suggestion) {
		window.location.href = '/admin/catalog/' + (suggestion.hasOwnProperty('price') ? 'p' : '') + suggestion.id;
	});
});
