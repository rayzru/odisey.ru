<div class="admin-catalog-search">
	<input type="text" class="admin-catalog-search__input typeahead" placeholder="Быстрый поиск">
</div>
<div class="admin-catalog-tree">
	<div><a href="/admin/catalog" class="main-catalog-link">\ Каталог</a></div>
	<div id="tree"></div>
</div>
<div class="admin-catalog-tools">
	<nav class="nav flex-column">
		<a href="/admin/catalog/import" class="nav-link">Импорт и пакетные операции</a>
		<a href="/admin/catalog/export" class="nav-link">Экспорт товаров в .CSV</a>
		<a href="/admin/service" class="nav-link">Обслуживание каталога</a>
	</nav>
</div>
<script type="text/javascript">
	var currentPath = [{if ($path && count($path) > 0)}{section name=id loop="$path"}{$path[id].id}{if !$smarty.section.id.last},{/if}{/section}{/if}];
	{literal}
	$(function () {
		$('#tree').fancytree({
			extensions: ["dnd"],
			dnd: {
				draggable: {
					zIndex: 1000,
					scroll: false,
					containment: "parent",
					revert: "invalid"
				},
				preventRecursiveMoves: true,
				preventVoidMoves: true,
				dragStart: function(node, data) {
					if ( data.originalEvent.shiftKey ) { return false; }
					return true;
				},
				dragEnter: function(node, data) {
					if (node.parent !== data.otherNode.parent) {
						return false;
					}
					return ["before", "after"];
				},
				dragDrop: function(node, data) {
					data.otherNode.moveTo(node, data.hitMode);
					const sorted = node.parent.children.map(item => item.data.id)
					$.post('/admin/catalog/sortTree', {data: sorted});
				}
			},
			icons: false,
			source: {url: "/admin/catalog/getTree"},
			cache: false,
			init: function (e, data) {
				if (currentPath.length) {
					var node;
					for (var i in currentPath) {
						node = data.tree.getNodeByKey(currentPath[i].toString());
						if (node.isFolder()) node.setExpanded(true);
					}

					node.setSelected();
					node.setFocus();
					node.setActive();
				}
				$('.fancytree-plain').siblings().sortable();
			},
		click: function (e, data) {
			var node = data.node, targetType = data.targetType;
			if (targetType === 'title') {
				document.location.href = "/admin/catalog/" + node.key;
			}
		}
		});

	});
</script>
{/literal}
