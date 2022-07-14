function resortItems(categoryId, items) {
	$.post("/admin/catalog/category/" + categoryId + "/reorder", {items: items});
}
