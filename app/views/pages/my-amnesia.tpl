<div class="container">
	<h1 class="page-title">Восстановление утерянного пароля</h1>
	<div class="row">
		<div class="col">
			<h4>Как восстановить?</h4>
			Если вы потеряли свой пароль и не можете его вспомнить, то достаточно ввести свой Email, указанный при регистрации. На ваш ящик в течении 5 минут будет отправлена ссылка, перейдя по которой вы получите новый пароль.
		</div>
		<div class="col">
			<div class="card bg-lightest">
				<div class="card-body">
					<form method="post" role="form">
						<div class="form-group">
							<input type="email" name="email" class="form-control {if $errors.email}is-invalid{/if}" id="inputEmail" placeholder="Email" required="required">
							{section name='id' loop="`$errors.email`"}<small class="invalid-feedback">{$errors.email[id]}</small>{/section}
						</div>
						<button type="submit" class="btn btn-primary btn-block">Восстановить</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col">
			<h4>Никому не передавайте пароль</h4>
			Ни в коем случае, ни под каким предлогом, никому не говорите свой пароль. Ни модераторам, ни представителям администрации он не нужен. Пароль должен оставаться тайной.
		</div>
	</div>
</div>
