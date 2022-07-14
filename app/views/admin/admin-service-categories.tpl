<table id="treetable" class="table categories">
	<col>
	<col width="120">
	<col width="120">
	<col width="120">
	<col width="120">
	<col width="120">
	<col width="120">
	<thead>
	<tr>
		<th></th>
		{foreach from=$qccKeys key=k item=qccKey}
			<th>{$qccKey}</th>
		{/foreach}
	</tr>
	</thead>
	<tbody>
	{strip}
		{section name=id loop=$tree}
			<tr data-tt-id="{$tree[id].id}"
					{if $tree[id].pid != 0}data-tt-parent-id="{$tree[id].pid}"{/if}>
				<td class="title"><a href="/admin/catalog/{$tree[id].id}">{$tree[id].title}</a></td>
				{foreach from=$qccKeys key=k item=qccKey}
					<td>
						<div class="progress">
							{foreach from=$qccValues key=j item=qccValue}
								<div class="progress-bar bg-{$qccValue.c}"
									 data-toggle="tooltip"
									 title="{$qccKey}: {$tree[id].qcc.perc.$k.$j}% {$qccValue.l}"
									 style="width:{$tree[id].qcc.perc.$k.$j}%;"></div>
							{/foreach}
						</div>
					</td>
				{/foreach}
			</tr>
		{/section}
	{/strip}
	</tbody>
</table>