{if !(empty($error))}
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		{$error}
	</div>
{/if}
<input name="email" type="text" placeholder="E-mail" class="form-control" tabindex="1" value="{$email}">

<small class="form-text mb-3"><a class="text-muted" href="/my/register">Регистрация</a></small>

<input name="password" type="password" placeholder="Пароль" class="form-control" tabindex="2" value="{$password}">
<small class="form-text mb-3"><a href="/my/amnesia" class="text-muted">Забыли пароль?</a></small>

<button type="submit" class="btn btn-outline-primary mb-3" tabindex="3">Войти</button>
