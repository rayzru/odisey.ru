<div class="catalog-content__header">
	<h3>
		{$item.title}
		<small>
			<a title="Открыть товар в каталоге" href="/catalog/p{$item.id}" target="_blank">
				<i class="fa fa-link"></i>
			</a>
		</small>
	</h3>
	{include file="partials/admin-breadcrumbs.tpl"}

</div>
<div class="catalog-content__data">
	{include file="admin/admin-catalog-item-form.tpl"}
</div>