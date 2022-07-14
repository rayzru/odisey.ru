<div class="catalog-content__header">
			<h3>Пользователи</h3>
	<div class="row">
		<div class="col">
			<div class="form-inline">
				<form>
					<input type="hidden" value="{$users.page}" name="page">
					<input type="text" value="{$users.query}" class="form-control form-control-sm" name="query">
					<button type="submit" class="btn btn-secondary btn-sm">Фильтр</button>
					{if (!empty($users.query))}<a href="/admin/users" class="btn btn-secondary btn-sm">Сбросить</a>{/if}
				</form>
			</div>
		</div>
		<div class="col">
			<nav aria-label="">
				<ul class="pagination pagination-sm justify-content-end">
					{if $users.pagerStart > 0}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
					{section name="page" start="`$users.pagerStart`" loop="`$users.pagerEnd`" }
						{assign var="si" value="`$smarty.section.page.index` + 1"}
						{math equation="$si" assign="i"}
						<li class="page-item {if $users.page == $i}active{/if}">
							<a class="page-link" href="?page={$i}">{$i}</a>
						</li>
					{/section}
					{if $users.pagerEnd < $users.pages}
						<li class="page-item disabled">
							<a class="page-link" href="#">...</a>
						</li>
					{/if}
				</ul>
			</nav>
		</div>
	</div>
</div>
<div class="catalog-content__data">
	<div class="container-fluid">
		<table class="table">
			<thead>
			<tr>
				<th title="Активность профиля"><i class="fa fa-check-circle-o"></i></th>
				<th>Тип</th>
				<th>Идентификатор</th>
				<th>Email</th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			{section name="id" loop="`$users.items`"}
				<tr id="row-{$users.items[id].id}" class="{if $users.items[id].flag_active === 0}table-active op{/if}">
					<td>{if $users.items[id].flag_active === 1}<i class="fa fa-check-circle"></i>{/if}</td>
					<td>
						{if $users.items[id].role == 'user'}
							<span class="badge badge-info">Пользователь</span>
						{elseif $users.items[id].role == 'admin'}
							<span class="badge badge-warning">Администратор</span>
						{/if}
					</td>
					<td><a href="/admin/users/{$users.items[id].id}/">{$users.items[id].identifier}</a></td>
					<td><a href="/admin/users/{$users.items[id].id}/">{$users.items[id].email}</a></td>
					<td>{$users.items[id].registered|date_format:"%d.%m.%Y"}</td>
					<td>{$users.items[id].last_login|date_format:"%d.%m.%Y"}</td>
				</tr>
			{/section}
			</tbody>
		</table>
	</div>
</div>
