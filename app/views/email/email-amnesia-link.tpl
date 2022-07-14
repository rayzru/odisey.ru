<h2>Здравствуйте!</h2>

<p>Сделан запрос на новый пароль для Вашей учетной записи на сайте odisey.ru.</p>
<p>Для смены пароля пройдите по ссылке или скопируйте ее в адресную строку браузера:</p>
<p>{$emailTemplate.domain}/my/amnesia/{$amnesia.key}</p>

{include file="email/email-button.tpl" href="`$emailTemplate.domain`/my/amnesia/`$amnesia.key`" title="Выслать новый пароль"}

<p>Если Вы не запрашивали восстановление пароля, проигнорируйте это сообщение.</p>