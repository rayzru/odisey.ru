<div class="container">
	<article>
		<h1 class="page-title">{$article.title}</h1>
		<small class="text-muted">{$article.created|date_format:"%d.%m.%Y"}</small>
		<p>
			{$article.text}
		</p>
		<div class="ya-share2 mb-3"
			 data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,evernote,viber,whatsapp,skype,telegram"
			 data-limit="3"
			 data-size="s"></div>
	</article>
</div>
