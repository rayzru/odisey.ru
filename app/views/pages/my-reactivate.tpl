<div class="container">
	<h1 class="page-title">Активация не завершена</h1>
	<div class="row">
		<div class="col">
			К сожалению ваша активация не может быть завершена. Полученная Вами ссылка уже использовалась, либо не верно
			указана. Вы можете попробовать повторно запросить активационную ссылку на свой почтовый адрес, указав его в
			форме.
		</div>
		<div class="col">
			<div class="card bg-lightest">
				<div class="card-body">
					<form method="post" action="/my/reactivate">
						<div class="form-group">
							<input type="email" value="{$reactivate.email}" name="email" placeholder="E-Mail"
								   class="form-control {if $errors.email}is-invalid{/if}" required="required">
							{section name='id' loop="`$errors.email`"}
								<small class="invalid-feedback">{$errors.email[id]}</small>{/section}
						</div>
						<button type="submit" class="btn btn-secondary">Отправить ссылку активации</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>

