<?php

namespace odissey;

$users = new UsersController();

$this->respond(
    'GET',
    '/users/?',
    function ($request, $response, $service, $app) use ($users) {
        $app->site->template = 'admin/admin-users';
        $page = $request->param('page') ? abs($request->param('page')) : 1;
        $query = $request->param('query');
        $users = $users->get(['page' => $page, 'query' => $query]);
        if ($users['count'] > 0 && $users['pages'] < $page) {
            $response
                ->redirect('?page='.$users['pages'].($query !== null ? '&query='.$users['query'] : ''))
                ->send();
        }
        $app->tpl->assign('users', $users);
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'GET',
    '/users/[i:id]/?',
    function ($request, $response, $service, $app) use ($users) {
        $app->site->template = 'admin/admin-user-profile';
        $app->site->addScript("/assets/js/admin-users.js");
        $user = $users->get(['id' => $request->id]);
        $app->tpl->assign('user', $user);
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'POST',
    '/users/[i:id]/recovery?',
    function ($request, $response, $service, $app) use ($users) {
        $user = $users->get(['id' => $request->id]);
        $email = $user['email'];
        $key = $users->addConfirmationKey($request->id);
        $mailer = new Mailer();
        $res = $mailer->compose(
            'Запрос восстановления пароля Odisey.ru',
            'email/email-amnesia-link.tpl',
            ['amnesia' => ['email' => $email, 'key' => $key]],
            [$email]
        );
        $response->json(['success' => $res]);
    }
);

$this->respond(
    'GET',
    '/users/[i:id]/[activate|deactivate:activity]/?',
    function ($request, $response, $service, $app) use ($users) {
        $f = $request->activity == 'activate' ? 1 : 0;
        $users->setActivity($request->id, $f);
        $response->redirect('/admin/users/'.$request->id)->send();
    }
);

$this->respond(
    'GET',
    '/users/[i:id]/[user|admin:role]/?',
    function ($request, $response, $service, $app) use ($users) {
        $users->setRole($request->id, $request->role);
        $response->redirect('/admin/users/'.$request->id)->send();
    }
);
