<div class="catalog-content__header">
	<h3>
		Обслуживание
	</h3>
	<ul class="nav nav-tabs card-header-tabs">
		<li class="nav-item">
			<a class="nav-link {if $action == ''}active{/if}" href="/admin/service">Старт</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {if $action === 'items'}active{/if}" href="/admin/service/items">Товары</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {if $action === 'categories'}active{/if}" href="/admin/service/categories">Разделы</a>
		</li>
	</ul>
</div>
<div class="catalog-content__data">
	<div id="loading">
		<div class="spnr">
			{section name=i loop=13 start=1 step=1}
				<div class="spnr-dot{$smarty.section.i.index} spnr-dot"></div>
			{/section}
		</div>
	</div>
	{if $action == 'items' || $action == 'categories'}
		{include file="admin/admin-service-`$action`.tpl"}
	{else}
		{include file="admin/admin-service-index.tpl"}
	{/if}
	{literal}<script>jQuery(function ($) {$("#loading").hide();});</script>{/literal}
</div>
