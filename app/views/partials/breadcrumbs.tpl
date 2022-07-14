<ol class="breadcrumb mt-3 mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
	{section name="i" loop="$breadcrumbs"}
		<li class="breadcrumb-item" itemprop="itemListElement" itemscope
			itemtype="https://schema.org/ListItem">
			<a title="{$breadcrumbs[i].title|escape}"
			   itemtype="https://schema.org/Thing"
			   itemscope
			   itemprop="item"
			   href="{$breadcrumbs[i].url}">
				<span itemprop="name">{$breadcrumbs[i].title}</span>
			</a>
			<meta itemprop="position" content="{$smarty.section.i.iteration}" />
		</li>
	{/section}
</ol>
