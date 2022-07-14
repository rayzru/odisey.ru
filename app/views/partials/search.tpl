<section class="interact-box">
	<div class="interact-box__wrapper">
		<div class="container">
			<form class="search-form" action="/search" id="search" method="get">
				<input type="text"
					   autocomplete="off"
					   class="search-form__input form-control form-control-lg typeahead"
					   name="term"
					   value="{$searchQuery}"
					   placeholder="Поиск по {$catalogItems} товарам">
				<a class="search-form__enter" onclick="$('#search').submit();return false;"></a>
				<input type="submit" class="search-form__submit">
			</form>
			{include file="partials/cabinet-nav.tpl"}
		</div>
	</div>
</section>
