<div class="catalog-content__header">
    <h3>{$user.email}</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a title="" href="/admin/users/">Пользователи</a>
        </li>
    </ol>

</div>
<div class="catalog-content__data">

    <div class="container">
        <div class="card-group">
            <div class="card">
                <div class="card-header">Роль</div>
                <div class="card-body">
                    <div class="mb-3">
                        {if $user.role =='admin'}
                            <div class="badge badge-warning">Администратор</div>
                        {else}
                            <div class="badge badge-primary">Пользователь</div>
                        {/if}
                    </div>
                    Роль пользователя определяет возможность доступа к административному ресурсу сайта.
                    Будте крайне бдительны предоставляя доступ новым пользователям.
                </div>
                <div class="card-footer">
                    <select class="form-control change-role"  rel="{$user.id}">
                        <option {if $user.role =='user'}selected{/if} value="user">Пользователь</option>
                        <option {if $user.role =='admin'}selected{/if} value="admin">Администратор</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Восстановление доступа</div>
                <div class="card-body">

                    Для сохранения приватности восстановление доступа пользователю осуществляется через стандартную
                    процедуру, которая отправляет письмо с ключом восстановления/ссылкой для сброса пароля на почту
                    профиля.
                </div>
                <div class="card-footer">
                    <button class="btn btn-outline-secondary send-recovery" rel="{$user.id}">Отправить письмо</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Активность профиля</div>
                <div class="card-body">
                    <div class="mb-3">
                        {if $user.flag_active == 0}
                            <div class="btn btn-outline-danger disabled btn-lg btn-block">Профиль деактивирован</div>
                        {else}
                            <div class="btn btn-outline-success disabled btn-lg btn-block">Профиль активен</div>
                        {/if}
                    </div>
                    Активность профиля указывает на возможность использования указанного электронного почтового адреса
                    на сайте.
                </div>
                <div class="card-footer">
                    {if $user.flag_active == 1}
                        <a href="/admin/users/{$user.id}/deactivate" class="btn btn-outline-danger">Отключить</a>
                    {else}
                        <a href="/admin/users/{$user.id}/activate" class="btn btn-outline-success">Включить</a>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

