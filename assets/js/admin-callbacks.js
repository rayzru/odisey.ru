jQuery(function ($) {
	$('.set-status').on('click', function (e) {
		e.preventDefault();
		$.post(this.href, function (res) {
			if (res.success) {
				window.location.reload();
			}
		});
		return false;
	});
});
