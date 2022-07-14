jQuery(function($){
	$('.set-status').on('click', function (e) {
		e.preventDefault();
		$.post(this.href, function (res) {
			if (res.success) {
				window.location.reload();
			}
		});
		return false;
	})

	function serializeForm() {
		var formData = $('#reviewForm').serializeArray();
		var data = {};
		$(formData).each(function(index, obj){	data[obj.name] = obj.value;	});
		return data;
	}

	$('#reviewForm').on('submit', function (e) {
		e.preventDefault();
		var data = serializeForm();
		$.post('/admin/reviews/' + data.id + '/ajax', data).done(function (res) {
			if (res.success) { document.location.reload(); }
		});
	})
});