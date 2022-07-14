jQuery(function ($) {
	$('.delete').on('click', function (e) {
		e.preventDefault();
		const url = this.href;
		const tr = $(this).closest('tr');
		$.ajax({
			url: url,
			method: 'DELETE',
			success: function (res) {
				if (res && res.success) {
					$(tr).remove();
				}
			}
		});
		return false;
	});


});

