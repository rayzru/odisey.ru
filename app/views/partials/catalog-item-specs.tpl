{if count($item.features)}
	{section name="id" loop="`$item.features`"}
		{if $short && $smarty.section.id.index < 5 || !$short}
			<dl class="catalog-item__specs">
				<dt><span>{$item.features[id].title}</span></dt>
				<dd>{$item.features[id].feature_value} {if $item.features[id].unit != ''}{$item.features[id].unit}{/if}</dd>
			</dl>
		{/if}
	{/section}
{/if}