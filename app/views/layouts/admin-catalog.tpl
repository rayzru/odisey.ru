{include file="layouts/admin-header.tpl"}
<div class="page-wrapper">
	{include file="partials/admin-navigation.tpl"}
	<div class="page-content">
		<div class="catalog-tree">
			{include file="partials/admin-catalog-navigation.tpl"}
		</div>
		<div class="catalog-content">
			{include file="`$site->template`.tpl"}
		</div>
	</div>
</div>
{include file="layouts/admin-footer.tpl"}