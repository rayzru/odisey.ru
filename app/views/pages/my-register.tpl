<div class="container">
	<h1 class="page-title">Регистрация</h1>
	<div class="row">

		<div class="col">
			<div class="card card-secondary bg-lightest mb-4">
				<div class="card-body">
					<div class="row">
						<div class="col">
							<h5>Обычная регистрация</h5>
							<form method="post" role="form">
								<div class="form-group ">
									<label>Email</label>
									<input type="email"
										   class="form-control {if $errors.email}is-invalid{/if}" id="inputEmail" placeholder="Email" required="required"
										   autocomplete="off" name="email"
										   value="{$register.email}">
									{section name='id' loop="`$errors.email`"}
										<small class="invalid-feedback">{$errors.email[id]}</small>{/section}
								</div>
								<div class="form-group">
									<label>Пароль</label>
									<input type="password" class="form-control {if $errors.password}is-invalid{/if}" id="inputPass"
										   name="password" autocomplete="off" required="required" placeholder="Пароль">
									{section name='id' loop="`$errors.password`"}
										<small class="invalid-feedback">{$errors.password[id]}</small>{/section}
								</div>

								<div class="form-group">
									<input type="password" class="form-control {if $errors.password}is-invalid{/if}" id="inputPass2"
										   name="password_confirmation"
										   placeholder="Подтверждение пароля" autocomplete="off" required="required">
								</div>

								<div class="form-check">
									<label class="form-check-label mb-4">
										<input type="checkbox" class="form-check-input" checked disabled>
										<small class="text-muted">
											Регистрируясь на сайте вы подтверждаете свое согласие с условиями
											<a href="/tos/" title="Открыть пользовательское соглашение">пользовательского соглашения и политики конфедициальности</a>.
										</small>
									</label>

									<button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-4 card-secondary bg-lightest">
				<div class="card-body">
					<h5>Регистрация через социальные сети</h5>
					<p>Что бы сэкономить ваше время, воспользуйтесь регистрацией через социальные сети.</p>
					{include file="partials/my-social-buttons.tpl"}
				</div>
			</div>
			{include file="partials/my-text-why-register.tpl"}
		</div>
		<div class="col">
			{include file="partials/my-text-confidentional.tpl"}
		</div>
	</div>
</div>

