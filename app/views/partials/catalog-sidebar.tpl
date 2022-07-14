<div class="catalog-sidebar">
	<div class="catalog-sidebar__marked">
		<header>Выбранные</header>
		<a rel="nofollow" href="#" class="catalog-sidebar__marked__button" onclick="compareMarked();">Сравнить</a>
		<a rel="nofollow" href="#" class="catalog-sidebar__marked__button" onclick="cartMarked(this);">В корзину</a>
		<a rel="nofollow" href="#" class="catalog-sidebar__marked__button catalog-sidebar__marked__button-reset"
		   class="clearMarkedLink" onclick="clearMarked();return false;">Сбросить</a>
	</div>
	<form onsubmit="applyFilter();return false;">
		<div class="catalog-sidebar__widget">
			<header>Наличие</header>
			<div class="form-check">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="item_stock" value="" checked>
					Любое
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="item_stock" value="stock">
					В наличии
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="item_stock" value="order">
					Под заказ
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label">
					<input class="form-check-input" type="radio" name="item_stock" value="none">
					Отсутствует
				</label>
			</div>

			<header>Стоимость</header>
			<div class="row no-gutters mb-3">
				<div class="col">
					<input type="number" class="form-control form-control-sm"
						   value="0" step="1" name="minPrice"
						   id="minPrice">
				</div>
				<div class="col-1"> &mdash;</div>
				<div class="col">
					<input type="number" class="form-control form-control-sm"
						   value="{if ($maxprice !== 0)}{$maxprice}{else}10000{/if}" step="1" name="maxPrice"
						   id="maxPrice">
				</div>
			</div>
			<input class="widget-slider" id="widget-slider-price" value="0;{if ($maxprice !== 0)}{$maxprice|number_format:2:".":""}{else}10000{/if}">
			<!--button class="btn btn-primary btn-block btn-sm mt-5"
					type="submit">Фильтровать <span id="totalCount"></span>
			</button-->
		</div>
	</form>
</div>

<script>
	var maxPrice = {$maxprice} || 10000;
	var totalCount = 0;

	{literal}

	function updateTotalCount(size) {
		$('#totalCount').text(size + plural(size, ' товар', ' товара', ' товаров'));
	}

	function filterItems() {
		var item_stock = $('[name=item_stock]:checked').val();
		var minPrice = $('#minPrice').val();
		var maxPrice = $('#maxPrice').val();

		var items = $('.catalog-items, .catalog-list-items')
			.filter(function () {
				var stock = (item_stock !== '') ? $(this).data("stock") === item_stock : true;
				var price = parseFloat($(this).data("price"));
				return stock && (price >= minPrice && price <= maxPrice);
			});
		updateTotalCount(items.length);
		return items;
	}

	function applyFilter() {
		$('.catalog-items, .catalog-list-items').hide();
		filterItems().show();
		return false;
	}

	$(function () {
		updateTotalCount($('.catalog-items').length);

		$("#widget-slider-price").ionRangeSlider({
			type: "double",
			min: 0,
			max: maxPrice,
			postfix: " р.",
			grid: true,
			onChange: function (data) {
				if (timer) clearTimeout(timer);
				timer = setTimeout(function () {
					$('#minPrice').val(data.from);
					$('#maxPrice').val(data.to);
					applyFilter();
				}, 400);
			},
		});

		var sliderPrice = $("#widget-slider-price").data("ionRangeSlider");
		var timer;
		$('#minPrice, #maxPrice').on('keyup', function () {
			if (timer) clearTimeout(timer);
			timer = setTimeout(function () {
				var min = $('#minPrice').val() || 0;
				var max = $('#maxPrice').val() || maxPrice;
				sliderPrice.update({
					from: min,
					to: max,
				})
				applyFilter();
			}, 400);
		})

		$('[name="item_stock"]').on('click', function () {
			applyFilter();
		});
	});
	{/literal}
</script>