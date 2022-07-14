<div>
	<a href="/admin/service/getReport/items" class="btn btn-outline-success"><i class="fa fa-file-excel-o"></i> Отчет по товарам в формате Excel</a>
	<!--a href="/admin/service/getReport/categories" class="btn btn-outline-dark"><i class="fa fa-file-excel-o"></i> Отчет по разделам</a-->
</div>

<div class="block">
	<a href="#" class="ml-2 btn btn-outline-secondary" id="recalcItems">Пересчитать количество товаров</a>
</div>

<div class="block">
	<a href="#" class="ml-2 btn btn-outline-secondary" id="repair">Восстановить дерево разделов</a>
</div>

<script>{literal}
	$('#recalcItems').click((e) => {
		$('#recalcItems').attr('disabled', true);
		e.preventDefault();
		$.get('/admin/service/catalog/recalcItems', function () {
			$('#recalcItems').attr('disabled', false);
		});
	});

	$('#repair').click((e) => {
		$('#repair').attr('disabled', true);
		e.preventDefault();
		$.get('/admin/service/catalog/rebuild', function () {
			$('#repair').attr('disabled', false);
		});
	});

	{/literal}
</script>