<div class="container-fluid catalog-compare">
	{include file="partials/breadcrumbs.tpl"}
	<h1 class="page-title">Сравнение: {$category.title}</h1>
	<div>
		<table class="table comparison table-hover">
			<thead>
			<tr>
				<th></th>
				{section name="i" loop="$items"}
					<th>
						<a href="/catalog/p{$items[i].id}">{$items[i].title}</a>
						<div><small class="text-muted">{$items[i].articul}</small></div>
						{if $items[i].price > 0}<b>{$items[i].price|number_format:2:".":""|replace:".00":''}<i class="icon-rouble"></i></b>{else}-{/if}

						<div><img class="img-thumbnail" width="50" src="{if $items[i].filename}{$items[i].filename|mediacachepath:'200x200'}{else}{'00-blank.jpg'|mediacachepath:'200x200'}{/if}" alt="{$items[i].title}">
						</div>
					</th>
				{/section}
			</tr>
			</thead>
			<tbody>
			{foreach from="`$features.list`" item="feature_id"}
				<tr>
					<td>{$featuresData[$feature_id].title}</td>
					{foreach from="`$features.items`" item="fi" key="item_id"}
						{if $fi.$feature_id != ''}
							<td>{$fi.$feature_id} {$featuresData[$feature_id].unit}</td>
						{else}
							<td class="text-muted">-</td>
						{/if}
					{/foreach}
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>

