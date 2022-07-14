<div class="catalog-content__header">
	<h3>Импорт и пакетные операции</h3>

	<ul class="nav nav-tabs card-header-tabs">
		<li class="nav-item">
			<a class="nav-link {if $action === 'import'}active{/if}"
			   href="/admin/catalog/import">Старт</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {if $action === 'prices'}active{/if}"
			   href="/admin/catalog/import/prices">Цены</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {if $action === 'titles'}active{/if}"
			   href="/admin/catalog/import/titles">Наименования</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {if $action === 'batch'}active{/if}"
			   href="/admin/catalog/import/batch">Пакетные операции</a>
		</li>
	</ul>
</div>
<div class="catalog-content__data">
	{if ($action == 'import' && $step == 'init')}
		{include file="admin/admin-catalog-import-dashboard.tpl"}
	{else}
		{include file="admin/admin-catalog-import-`$action`-`$step`.tpl"}
	{/if}
</div>