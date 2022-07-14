<div class="container" style="margin-bottom: 2em;">
	<div class="row">
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
			<h2 class="page-title">Новости</h2>
			{section name="id" loop="$news"}
				<article>
					<h6>
						<a href="/feed/{$news[id].id}-{$news[id].slug}/">{$news[id].title}</a>
						<small class="text-muted">{$news[id].created|date_format:"%d.%m.%Y"}</small>
					</h6>
				</article>
			{/section}
		</div>
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
			<h2 class="page-title">Статьи</h2>
			{section name="id" loop="$articles"}
				<article>
					<h6>
						<a href="/feed/{$articles[id].id}-{$articles[id].slug}/">{$articles[id].title}</a>
						<small class="text-muted">{$articles[id].created|date_format:"%d.%m.%Y"}</small>
					</h6>
				</article>
			{/section}
		</div>
	</div>
</div>

