<div class="catalog-item__qa">
	{section name="id" loop="$qa"}
		<div class="text-muted pull-right">
			<time datetime="{$qa[id].published_date}">{$qa[id].published_date|date_format}</time>
		</div>
		<h4>{$qa[id].question}</h4>
		<div class="" style="position: relative;">
			<div class="popover bottom answer" style="display: block;">
				<div class="arrow"></div>
				<div class="popover-content">
					{$qa[id].answer}
					<p class="text-muted" style="margin-top: 5px;"><i class="icon-user"></i>
						Сотрудник компании Одиссей</p>
				</div>
			</div>
		</div>
	{sectionelse}
	<p class="text-muted">Консультации отсутствуют</p>
	<p class="small">
		<a rel="nofollow" href="#" data-toggle="modal" data-target="#ask">Задать вопрос специалисту</a>
	</p>
{/section}
</div>

<form method="post" action="/qa/add/{$item.item_id}" id="formQA">

	<div class="modal fade" id="ask" tabindex="-1" role="dialog"
		 aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
							aria-hidden="true">&times;
					</button>
					<h4 class="modal-title">Задать вопрос специалистам</h4>
				</div>
				<div class="modal-body">
					<p>Обратите внимание, возможно ответ на ваш вопрос уже опубликован во вкладке
						"Консультации" данного товара. Имейте ввиду, отправляя вопрос вы адресуете
						его
						нашим специалистам к товару <span
								class="label label-default">{$item.item_title}</span></p>
					{if !$user->logged}
						<div class="form-group">
							<label>Ваше имя</label>
							<input type="text" name="name" value="" class="form-control">
						</div>
						<div class="form-group">
							<label>Email</label>
							<input type="email" name="email" value="" class="form-control"
								   required="required">
						</div>
						<div class="form-group">
							<label>Телефон для связи</label>
							<input type="text" name="phone" value="" class="form-control">
						</div>
					{else}
						<div class="form-group">
							<label>Вы зашли как</label>
							<div class="form-control-static">{$user->account->login}</div>
						</div>
					{/if}

					<div class="form-group">
						<label>Ваш вопрос</label>
						<textarea name="question" class="form-control" rows="5"></textarea>
					</div>

					<div class="form-group">
						<label>Решите, сколько будет {$captcha}</label>
						<input type="text" name="captcha" class="form-control">
					</div>

					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">
						Закрыть
					</button>
					<button type="submit" class="btn btn-success" id="submitQA">Отправить вопрос
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

{literal}
	<script>
		$(function () {
			$('#formQA').submit(function (e) {
				e.preventDefault();
				var form = this;
				$.post(form.action, $(form).serialize(), function (response) {
					if (response == 'ok') {
						form.question.value = '';
						$('#ask').modal('toggle');
						showNotify('<h4>Ваш вопрос был отправлен</h4>Ожидайте ответа от наших специалистов в указанной Вами электронной почте<br/>или на странице товара в качестве консультации.');
					} else {
						if (response == 'error') showNotify('<h4>Ошибка добавления вопроса</h4>Попробуйте обратится позже.');
						if (response == 'captcha error') showNotify('<h4>Вы ввели неправильное решение</h4>Для защиты от спама, мы вынуждены проверять вас на человечность, простите. Повторите, пожалуйста ввод вашего вопроса и решите небольшую задачку в конце нашей формы.');
						//$('#ask').modal('close');
					}
				});
			});
		});

	</script>
{/literal}