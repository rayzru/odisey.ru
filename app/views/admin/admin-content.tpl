<div class="catalog-content__header">
	<h3>Новости и статьи</h3>
	<a href="/admin/content/add" class="btn btn-primary btn-sm">Новая запись</a>
</div>
<div class="catalog-content__data">
	<div class="container-fluid">
		<table class="table">
			<thead>
			<tr>
				<th title="Активность товара"><i class="fa fa-check-circle-o"></i></th>
				<th>Тип</th>
				<th>Заголовок</th>
				<th>Создано</th>
				<th>Опубликовано</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name="id" loop="$content"}
				<tr id="row-{$content[id].id}" class="{if $content[id].flag_active === 0}table-active{/if}">
					<td>{if $content[id].flag_active === 1}<i class="fa fa-check-circle"></i>{/if}</td>
					<td>
						{if $content[id].type == 'news'}
							<span class="badge badge-info">Новости</span>
						{elseif $content[id].type == 'system'}
							<span class="badge badge-warning">Системный</span>
						{elseif $content[id].type == 'article'}
							<span class="badge badge-primary">Статьи</span>
						{elseif $content[id].type == 'content'}
							<span class="badge badge-default">Текст</span>
						{/if}
					</td>
					<td><a href="/admin/content/{$content[id].id}/">{$content[id].title}</a></td>
					<td>{$content[id].created|date_format:"%d.%m.%Y"}</td>
					<td>{$content[id].publish|date_format:"%d.%m.%Y"}</td>
					<td class="text-nowrap">
						<a href="/admin/content/{$content[id].id}/"
						   class="btn btn-outline btn-outline-info btn-sm"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="deleteContent({$content[id].id})"
						   class="btn btn-outline btn-outline-danger btn-sm"><i class="fa fa-times"></i></a>
					</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>
{literal}
<script>
	function deleteContent(id) {
		if (confirm('Подтвердите удаление записи?')) {
			$.ajax({
				url: '/admin/content/' + id,
				type: 'DELETE',
				success: function () {
					$('tr#row-' + id).remove();
				}
			});
		}
	}

</script>
{/literal}