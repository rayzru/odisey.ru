<div class="container">

	<h1 class="page-title">{$site->title}</h1>

	<p class="text-muted">
		Не можете найти Ваш раздел? Воспользуйтесь поиском выше!
	</p>
	<div class="card-columns card-columns-wrapper">
		{section name=i loop=$categories}
			<div class="card catalog-categories">
				<div class="card-body">
					<h3 title="{$categories[i].title}">
						<a rel="canonical"
						   href="/catalog/{$categories[i].id}-{$categories[i].title|transliterate|lower}">{$categories[i].title}</a>
					</h3>
					{section name=j loop=$categories[i].children}
						<a rel="canonical"
						   href="/catalog/{$categories[i].children[j].id}-{$categories[i].children[j].title|transliterate|lower}"
						   title="{$categories[i].children[j].title}">{$categories[i].children[j].title}</a>
					{/section}
				</div>
			</div>
		{/section}
	</div>
</div>
