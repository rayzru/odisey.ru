var qccKeys = {
	none: {
		l: 'Пусто',
		c: 'danger'
	},
	error: {
		l: 'Ошибка',
		c: 'danger'
	},
	weak: {
		l: 'Мало',
		c: 'warning'
	},
	overflow: {
		l: 'Много',
		c: 'warning'
	},
	normal: {
		l: 'Норма',
		c: 'success'
	}
};

var qccValues = {
	qcc_description: 'Описание',
	qcc_seo_description: 'SEO Описание',
	qcc_keywords: 'Ключевые слова',
	qcc_articul: 'Артикул',
	qcc_images: 'Изображения',
	qcc_features: 'Характеристики',
	qcc_price: 'Цены'
};

jQuery(function ($) {
	$('.qcc-ckeck-btn').on('click', function (e) {
		$btn = $(this);
		$btn.addClass('disabled').text('Выполняется актуализация...').prepend("<i class='fa fa-spin fa-spinner'>&nbsp;");
		$.ajax({
			url: '/admin/service/qcc/check',
			success: function (response) {
				$btn
					.removeClass('disabled')
					.removeClass('btn-outline-secondary')
					.addClass('btn-outline-success')
					.text('Актуализация закончена')
					.prepend("<i class='fa fa-check'>&nbsp;");
			}
		});
	});

	$("#treetable.items").treetable({
		expandable: true,
		clickableNodeNames: true,
		allowUnsortedHtml: true,
		persist: true,
		persistStoreName: "qccItems",
		loadBranches: true,
		onInitialized: function (e) {
			$("#loading").hide();
			$("#treetable").show();
		},
		onNodeExpand: function (e) {
			getItems(this.id);
		}
	});

	$("#treetable.categories").treetable({
		expandable: true,
		clickableNodeNames: true,
		allowUnsortedHtml: true,
		persist: true,
		persistStoreName: "qccCategories",
		loadBranches: true,
		onInitialized: function (e) {
			$("#loading").hide();
		}
	});

	$("#treetable tbody").on("mousedown", "tr", function () {
		$(".selected").not(this).removeClass("selected");
		$(this).toggleClass("selected");
	});

	function getItems(parentId) {
		$("#loading").show();
		$.ajax({
			type: 'POST',
			url: '/admin/service/qcc/items',
			data: {
				pid: parentId
			},
			success: function (data) {
				$("#loading").hide();
				var childNodes = data;

				if (childNodes) {
					var parentNode = $("#treetable").treetable("node", parentId);

					for (var i = 0; i < childNodes.length; i++) {
						var node = childNodes[i];
						var nodeToAdd = $("#treetable").treetable("node", node['id']);
						if (!nodeToAdd) {
							var row = '<tr data-tt-id="' + node['id'] + '" data-tt-parent-id="' + parentId + '">';
							row += "<td><a href='/admin/catalog/p" + node['id'] + "'>" + node['title'] + "</a> <small class='text-muted float-right'>" + node['articul'] + "</small></td>";
							$.each(qccValues, function (key, value) {
								row += "<td><div class='badge badge-" + qccKeys[node[key]].c + "'>" + qccKeys[node[key]].l + "</div></td>";
							});
							row += "</tr>";

							$("#treetable").treetable("loadBranch", parentNode, row);
						}
					}
				}
			},
			error: function (error) {
				$("#loading").hide();
				console.error('Error loading items', error);
			},
			dataType: 'json'
		});
	}
});