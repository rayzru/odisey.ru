jQuery(function ($) {
	$('.send-recovery').on('click', function (e) {
		e.preventDefault();
		$btn = $(this);
		const id = $(e.target).attr('rel');
		$btn.html("<i class='fas fa-spin fa-spinner'></i> Отправление...").attr("disabled", true);
		$.post('/admin/users/' + id + '/recovery', {}, function (res) {
			if (res && res.success) {
				$btn.html("<i class='fas fa-check'></i> Отправлено").addClass('btn-outline-success');
			}
		})
	})

	$('.change-role').on('change', function (e) {
		e.preventDefault();
		if (confirm('Вы действительно хотите сменить роль пользователя?')) {
			$btn = $(this);
			const id = $(e.target).attr('rel');
			$btn.attr("disabled", true);
			window.location.href = '/admin/users/' + id + '/' + $btn.val();
		}
	})
});
