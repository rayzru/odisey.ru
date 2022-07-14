<div class="import-items">
	<form method="post" enctype="multipart/form-data" action="/admin/catalog/import/batch/upload">
		<legend>Загрузка или ввод перечня артикулов</legend>
		<div class="row">
			<div class="col">
				<div class="form-group dropzone">
					<input type="file" id="upload" name="file" class="form-control">
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<textarea rows="" placeholder="Введите перечень артикулов" class="form-control" name="articuls"></textarea>
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-primary">Загрузить</button>
	</form>
</div>