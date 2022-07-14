<div class="container">
	<h1 class="page-title">{$account->email}</h1>
	<div class="row profile">
		<div class="col">
			<form method="post">
				{if $profileupdated}
					<div class="alert alert-success" role="alert">
						Профиль обновлен
					</div>
				{/if}
				<div class="form-group">
					<label>Имя</label>
					<input type="text" class="form-control" value="{$account->identifier}" name="identifier" maxlength="100">
				</div>

				<div class="form-group">
					<label>Контактный телефон</label>
					<input type="tel" class="form-control" value="{$account->phone}" name="phone" maxlength="30">
				</div>

				<div class="form-group" id="passwordLink">
					<a rel="nofollow" href="#" class="passwordToggler">Сменить пароль</a>
				</div>
				<div class="form-group {if $passworderror}has-error{/if}" id="passwordForm">
					<div class="form-group">
						<a rel="nofollow" href="#" class="passwordToggler">Не менять пароль</a>
					</div>
					{if $passworderror}
						<div class="alert alert-danger" role="alert">
							<strong>Пароли не совпадают</strong>
							<br/>Введенное подтверждение парля не совпадает с паролем из первого поля.
							Введите пароли повторно.
						</div>
					{/if}
					<div class="row">
						<div class="col">
							<label>Новый пароль</label>
							<input type="password" class="form-control" value="" autocomplete="off" name="password">
						</div>
						<div class="col">
							<label>Подтверждение пароля</label>
							<input type="password" class="form-control" value="" autocomplete="off" name="password2">
						</div>
					</div>
				</div>

				<div class="form-group">
					<button class="btn btn-primary" type="submit">Сохранить</button>
				</div>

			</form>
		</div>
		<div class="col-4">

		</div>
	</div>
</div>

{if $passworderror}{literal}<script>$(function(){$('#passwordForm, #passwordLink').toggle();})</script>{/literal}{/if}