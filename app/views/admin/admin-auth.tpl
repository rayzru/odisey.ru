<div class="container" style="margin-top: 4em;">
	<div style="width: 400px; margin: 0 auto;">
		<h2 >Панель управления</h2>

		<form action="/admin" method="post" class="mb-lg-5">
			{if !(empty($error))}
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					{$error}
				</div>
			{/if}
			<input name="email" type="text" placeholder="E-mail" class="form-control mb-3" tabindex="1"
				   value="{$email}">

			<input name="password" type="password" placeholder="Пароль" class="form-control mb-3" tabindex="2"
				   value="{$password}">

			<span class="text-muted float-right" style="margin-bottom: 2em;" title="Ваш IP адрес">{$ip}</span>

			<button type="submit" class="btn btn-outline-primary mb-3" tabindex="3">Войти</button>

		</form>
	</div>
</div>