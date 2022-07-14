<div class="modal" tabindex="-1" role="dialog" id="categoryModal" aria-labelledby="categoryModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="/admin/catalog/create-category" method="post">
			<div class="modal-header">
				<h5 class="modal-title" id="categoryModalLabel">Новый раздел</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
					<input type="hidden" name="parent" value="0">
					<div class="form-group">
						<label for="modal-category-title" class="col-form-label">Наименование</label>
						<input type="text" class="form-control" name="title" id="modal-category-title">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Создать</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
			</div>
			</form>
		</div>
	</div>
</div>
<script>
	{literal}
	jQuery(function ($) {
		$('#categoryModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var parent = button.data('parent');
			var modal = $(this);
			modal.find('.modal-body input[name=parent]').val(parent);
			$('#modal-category-title').val('');
			setTimeout(function () {
				$('#modal-category-title').focus();
			}, 300)
		});

		$('#categoryModal button:submit').on('click', function (e) {
			$('#categoryModal button:submit').prop('disabled', true);
			$('#categoryModal form').submit();
		});
	});
	{/literal}
</script>


<div class="modal" tabindex="-1" role="dialog" id="removeCategoryModal" aria-labelledby="removeCategoryModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="removeCategoryModalLabel">Удалить раздел</h5>

			</div>
			<div class="modal-body">
				Данная операция безвозвратно уничтожит все данные разделов и подразделов,
				а так же вложенных товаров, их характеристик и зависимых данных.
			</div>
			<div class="modal-footer">
				<button type="button" id="confirmDelete" onclick="return confirm('Последнее предупреждение. Подтвердите операцию удаления.');" class="btn btn-danger">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
			</div>
		</div>
	</div>
</div>

<script>
	{literal}
	jQuery(function ($) {
		$('#removeCategoryModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var parent = button.data('parent');
			var modal = $(this);
			modal.find('#confirmDelete').val(parent);
		});

		$('#confirmDelete').on('click', function () {
			var id = $(this).val();
			$.ajax({
				url: '/admin/catalog/' + id,
				type: 'DELETE',
				success: function(result) {
					if (parseInt(result) != 0) {
						document.location.href = '/admin/catalog/' + result;
					} else {
						document.location.href = '/admin/catalog/';
					}
				}
			});
		});
	});
	{/literal}
</script>
