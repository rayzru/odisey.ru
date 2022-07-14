function deleteFeature(el) {
	if (confirm('Вы действительно хотите удалить характеристику?')) {
		$.ajax({
			url: '/admin/features/' + el,
			type: 'DELETE',
			success: function() {
				window.location.reload();
			}
		});
	}
	return false;
};
