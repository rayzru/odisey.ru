<ol class="breadcrumb">
	{section name="i" loop="$breadcrumbs"}
		<li class="breadcrumb-item">
			<a title="{$breadcrumbs[i].title|escape}"
			   href="{$breadcrumbs[i].url}">{$breadcrumbs[i].title}</a>
		</li>
	{/section}
</ol>
