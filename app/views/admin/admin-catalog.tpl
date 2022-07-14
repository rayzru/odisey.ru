<div class="catalog-content__header">
	<h3>
		{$category.title}
		<small>
			<a title="Открыть раздел каталога" href="/catalog/{$category.id}" target="_blank">
				<i class="fa fa-link"></i>
			</a>
		</small>
	</h3>
	{include file="partials/admin-breadcrumbs.tpl"}

	<ul class="nav nav-tabs card-header-tabs">
		<li class="nav-item">
			<a class="nav-link {if (empty($action) && $category.is_leaf === 0) || $action === 'category'}active{/if}"
			   href="/admin/catalog/{$category.id}/category">Раздел</a>
		</li>
		{if $category.is_leaf === 1}
			<li class="nav-item">
				<a class="nav-link {if (empty($action) && $category.is_leaf === 1) || $action === 'items'}active{/if}"
				   href="/admin/catalog/{$category.id}/items">Товары</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {if $action === 'additem'}active{/if}"
				   href="/admin/catalog/{$category.id}/additem">Добавить товар</a>
			</li>
		{/if}
		<li class="nav-item ml-auto">
			<a class="nav-link" href="#" data-parent="{$category.id}" data-toggle="modal" data-target="#categoryModal">
				Добавить подраздел
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#" data-parent="{$category.id}" data-toggle="modal" data-target="#removeCategoryModal">
				Удалить раздел
			</a>
		</li>
	</ul>
</div>
<div class="catalog-content__data">
	{include file="partials/admin-catalog-category-modal.tpl"}
	{if empty($action)}
		{if $category.is_leaf === 1}
			{include file="admin/admin-catalog-items.tpl"}
		{else}
			{include file="admin/admin-catalog-category-form.tpl"}
		{/if}
	{else}
		{if $action === 'category' || $action === 'addcategory'}
			{include file="admin/admin-catalog-category-form.tpl"}
		{elseif $action === 'items'}
			{include file="admin/admin-catalog-items.tpl"}
		{elseif $action === 'additem'}
			{include file="admin/admin-catalog-item-form.tpl"}
		{/if}
	{/if}
</div>
