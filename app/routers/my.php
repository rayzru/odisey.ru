<?php

namespace odissey;

use Klein\Klein;

$catalog = new CatalogController();
$account = new AccountController();
$users = new UsersController();
$orders = new OrdersController();

$this->respond(
    ['GET', 'POST'],
    '/[:ctrl]?',
    function ($request, $response, $service, $app) use ($account) {
        $app->site->addScript('/assets/js/my.js');
        $app->site->addScript("/assets/js/jivosite.js", false);
        $app->tpl->assign('controlURL', $request->ctrl);
    }
);

$this->respond(
    'GET',
    '/?',
    function ($request, $response, $service, $app) use ($account) {
        if (!$account->isLogged()) {
            $response->redirect('/my/auth');
        } else {
            $app->site->template = 'pages/my-index';
            $app->tpl->display('layouts/frontend-default.tpl');
        }
    }
);

$this->respond(
    ['GET', 'POST'],
    '/auth/?',
    function ($request, $response, $service, $app) use ($account) {
        if ($request->method() === 'POST') {
            if ($account->authEmail($request->email, $request->password)) {
                $data = $account->getAccountByEmail($request->email);
                $account->setAccount($data);
            } else {
                $app->tpl->assign('email', $request->email);
                $app->tpl->assign('password', $request->password);
                $app->tpl->assign('error', '<h4>Ошибка авторизации</h4>Логин или пароль указаны не верно.');
            }
        }

        if ($account->isLogged()) {
            $response->redirect('/my');
        } else {
            $app->site->template = 'pages/my-auth';
            $app->tpl->display('layouts/frontend-default.tpl');
        }
    }
);

$this->respond(
    'GET',
    '/authsocial/[:provider]/?',
    function ($request, $response, $service, $app) use ($account, $users) {
        $provider = $request->provider;
        $HA = new \Hybridauth\Hybridauth(Configuration::hybridAuthConfig());
        try {
            $adapter = $HA->authenticate($provider);
            $isConnected = $adapter->isConnected();
            if ($isConnected) {
                //Retrieve the user's profile
                $userProfile = $adapter->getUserProfile();
                if (!$account->isEmailUsed($userProfile->email)) {
                    $newPassword = Helpers::genPassword();
                    $user_id = $account->register($userProfile->email, $newPassword, null);
                    $users->activate($user_id);
                }
                $data = $account->getAccountByEmail($userProfile->email);
                $account->setAccount($data);
            }
        } catch (\Exception $exception) {
            // none
        }

        $response->redirect('/my');
    }
);

$this->respond(
    ['POST', 'GET'],
    '/register/?',
    function ($request, $response, $service, $app) use ($account, $users) {

        if ($account->isLogged()) {
            $response->redirect('/my')->send();
        }

        if ($request->method('POST')) {
            $factory = new Validation();

            $factory->extend(
                'email_used',
                function ($attribute, $value, $parameters) use ($account) {
                    return !$account->isEmailUsed($value);
                },
                'Email already exist'
            );

            $rules = [
                'email' => ['required', 'email', 'email_used'],
                'password' => ['required', 'min:5', 'confirmed', 'alpha_num'],
                'password_confirmation' => ['required'],
            ];

            $messages = [
                'email.required' => 'Обязательное поле',
                'email.email' => 'Некорректный Email',
                'email.email_used' => 'Данные Email уже зарегистрирован. Укажите другой email.',
                'password.required' => 'Обязательное поле',
                'password.min' => 'Пароль должен быть не короче :min символов',
                'password.confirmed' => 'Подтверждение пароля не совпадает с введенным вами паролем',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            if ($validator->fails()) {
                $e = $validator->messages()->messages();
                $app->tpl->assign('errors', $e);
                $app->tpl->assign('register', $request->params());
            } else {
                if ($user_id = $account->register($request->email, $request->password, $request->identity)) {
                    $key = $users->addConfirmationKey($user_id);

                    $mailer = new Mailer();

                    $sent = $mailer->compose(
                        'Подтверждение регистрации на сайте Odisey.ru',
                        'email/email-activate.tpl',
                        ['activate' => ['email' => $request->email, 'key' => $key]],
                        [$request->email]
                    );
                    if ($sent) {
                        $response->redirect('/my/registered')->send();
                    } else {
                        $app->tpl->assign('errors', ['email' => ['Проблема с отсылкой уведомлений. Попробуйте позже.']]);
                        $app->tpl->assign('register', $request->params());
                    }
                }
            }
        }

        $app->site->template = 'pages/my-register';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/registered/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-registered';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/activated/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-activated';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    ['GET', 'POST'],
    '/amnesia/?',
    function ($request, $response, $service, $app) use ($account, $users) {
        if ($request->method('POST')) {
            $factory = new Validation();

            $factory->extend(
                'email_used',
                function ($attribute, $value, $parameters) use ($account) {
                    return $account->isEmailUsed($value);
                },
                'Email not exist'
            );

            $factory->extend(
                'email_active',
                function ($attribute, $value, $parameters) use ($account) {
                    return $account->isEmailActive($value);
                },
                'Profile with given email is not active'
            );

            $rules = [
                'email' => ['required', 'email', 'email_used', 'email_active'],
            ];

            $messages = [
                'email.required' => 'Обязательное поле',
                'email.email' => 'Некорректный Email',
                'email.email_used' => 'Указанный Email не зарегистрирован.',
                'email.email_active' => 'Профиль пользователя с указанным Email не активен.'.
                    ' Веротяно Вы его не активировали. '.
                    '<a href="/my/reactivate/">Запросите ссылку активации</a> на почту повторно. '.
                    'Так же, возможно, что данный аккаунт был заблокирован администрацией.'.
                    'По данному вопросу связжитесь с нашими менеджерами.',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                $app->tpl->assign('amnesia', $request->params());
            } else {
                $acc = $account->getAccountByEmail($request->email);
                if ($acc) {
                    $mailer = new Mailer();
                    $key = $users->addConfirmationKey($acc['id']);
                    if ($mailer->compose(
                        'Запрос восстановления пароля Odisey.ru',
                        'email/email-amnesia-link.tpl',
                        ['amnesia' => ['email' => $request->email, 'key' => $key]],
                        [$request->email]
                    )) {
                        $response->redirect('/my/amnesia-sent')->send();
                    }
                }
            }
        }
        $app->site->template = 'pages/my-amnesia';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/amnesia-sent/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-amnesia-sent';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/amnesia/[:key]/?',
    function ($request, $response, $service, $app) use ($users, $account) {
        $user_id = $users->revealConfirmationKey($request->key);
        if ($user_id) {
            $data = $account->getAccountById($user_id);
            $newPass = Helpers::genPassword();
            if ($users->updatePassword($user_id, $newPass)) {
                $mailer = new Mailer();

                if ($mailer->compose(
                    'Новый пароль Odisey.ru',
                    'email/email-amnesia-pass.tpl',
                    ['p' =>['email' => $data['email'], 'pass' => $newPass]],
                    [$data['email']]
                )) {
                    echo json_encode(['success' => true]);
                    $response->redirect('/my/amnesia-reset/')->send();
                }
            };
        }
        $response->redirect('/my/amnesia-fail/')->send();
    }
);

$this->respond(
    'GET',
    '/amnesia-reset/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-amnesia-reset';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);
$this->respond(
    'GET',
    '/amnesia-fail/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-amnesia-fail';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/activate/[:key]/?',
    function ($request, $response, $service, $app) use ($users, $account) {
        if ($user_id = $users->revealConfirmationKey($request->key)) {
            $users->activate($user_id);
            $data = $account->getAccountById($user_id);
            $account->setAccount($data);
            $response->redirect('/my/activated/')->send();
        } else {
            $response->redirect('/my/reactivate/')->send();
        }
    }
);

$this->respond(
    ['GET', 'POST'],
    '/reactivate/?',
    function ($request, $response, $service, $app) use ($account, $users) {
        if ($request->method('POST')) {
            $factory = new Validation();

            $factory->extend(
                'email_used',
                function ($attribute, $value, $parameters) use ($account) {
                    return $account->isEmailUsed($value);
                },
                'Email not exist'
            );

            $factory->extend(
                'email_active',
                function ($attribute, $value, $parameters) use ($account) {
                    return !$account->isEmailActive($value);
                },
                'Profile with given email is active'
            );

            $rules = [
                'email' => ['required', 'email', 'email_used', 'email_active'],
            ];

            $messages = [
                'email.required' => 'Обязательное поле',
                'email.email' => 'Некорректный Email',
                'email.email_used' => 'Указанный Email не зарегистрирован.',
                'email.email_active' => 'Профиль пользователя с указанным Email уже активен. '.
                    'Нет надобности активировать. '.
                    'Если это ваш профиль но Вы не помните пароля, '.
                    'воспользуйтесь <a href="/my/amnesia">формой восстановления пароля</a>.',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                $app->tpl->assign('reactivate', $request->params());
            } else {
                $acc = $account->getAccountByEmail($request->email);
                if ($acc) {

                    $key = $users->addConfirmationKey($acc['id']);


                    $mailer = new Mailer();
                    $subject = 'Подтверждение регистрации на сайте Odisey.ru';

                    if ($mailer->compose(
                        $subject,
                        'email/email-activate.tpl',
                        ['activate' => ['email' => $request->email, 'key' => $key]],
                        [$request->email]
                    )) {
                        $response->redirect('/my/registered')->send();
                    }
                }
            }
        }
        $app->site->template = 'pages/my-reactivate';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/tos/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/my-tos';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'POST',
    '/cart/add/?',
    function ($request, $response) use ($orders, $account) {
        if ($account->isLogged()) {
            if (is_numeric($request->id)) {
                $quantity = (isset($request->quantity) && $request->quantity > 0) ? $request->quantity : 1;
                $orders->addCartItem($request->id, $quantity);
                $response->json(['status' => 'success']);
            } elseif (is_array($request->id)) {
                if (count($request->id) > 0) {
                    foreach ($request->id as $id) {
                        $orders->addCartItem($id);
                    }
                    $response->json(['status' => 'success']);
                }
            }
        } else {
            if (is_numeric($request->id)) {
                $quantity = (isset($request->quantity) && $request->quantity > 0) ? $request->quantity : 1;
                $orders->addGuestCartItem($request->id, $quantity);
                $response->json(['status' => 'success']);
            } elseif (is_array($request->id)) {
                if (count($request->id) > 0) {
                    foreach ($request->id as $id) {
                        $orders->addGuestCartItem($id);
                    }
                    $response->json(['status' => 'success']);
                }
            }
        }
    }
);

$this->respond(
    'POST',
    '/cart/remove/?',
    function ($request, $response, $service, $app) use ($orders) {
        if ($orders->deleteCartItem($request->id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
);

$this->respond(
    'GET',
    '/cart/clear/?',
    function ($request, $response, $service, $app) use ($orders) {
        $orders->clearCart();
        $response->redirect('/my/cart/');
    }
);

$this->respond(
    'POST',
    '/cart/update/?',
    function ($request, $response, $service, $app) use ($orders) {
        foreach ($_POST['cart'] as $item_id => $qty) {
            $orders->updateCartItem($item_id, $qty);
        }
        $response->redirect('/my/cart')->send();
    }
);

$this->respond(
    'GET',
    '/cart/?',
    function ($request, $response, $service, $app) use ($orders, $catalog, $account) {
        $app->site->template = 'pages/my-cart';
        $app->tpl->assign('cart', $orders->getCart());
        $app->tpl->assign('stocks', $catalog->getStockStrings());

        $app->tpl->assign('warningsStrings', $orders->cartWarnings);
        $app->tpl->assign('status', $orders->getCartStatuses());

        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/cart/json/?',
    function ($request, $response, $service, $app) use ($orders, $catalog, $account) {
        $cart = $orders->getCart();
        $response->json(
            [
                "count" => count($cart),
                "items" => $cart,
            ]
        );
    }
);


$this->respond(
    'GET',
    '/orders/?',
    function ($request, $response, $service, $app) use ($orders, $account) {
        $app->site->template = 'pages/my-orders';
        $profile = $account->getAccount();
        $app->tpl->assign('ordersStatuses', $orders->statuses);
        $app->tpl->assign(
            'orders',
            $orders->getOrders(
                [
                    'user' => $profile->id,
                    'status' => ['added', 'queued'],
                ]
            )
        );
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/orders/[i:id]/?',
    function ($request, $response, $service, $app) use ($orders, $account, $catalog) {
        $order = $orders->getOrders(['id' => $request->id, 'user' => $account->getAccount()->id]);
        if (!empty($order)) {
            $app->tpl->assign('order', $request->id);
            $app->tpl->assign('ordersStatuses', $orders->statuses);
            $app->tpl->assign('stocks', $catalog->getStockStrings());
            $app->tpl->assign('items', $orders->getOrderItems($request->id, $account->getAccount()->id));
        } else {
            header('HTTP/1.1 404 Not Found');
            $app->tpl->assign('error', true);
        }
        $app->site->template = 'pages/my-order';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/orders/archive/?',
    function ($request, $response, $service, $app) use ($orders, $account) {
        $app->site->template = 'pages/my-orders-archive';
        $profile = $account->getAccount();
        $app->tpl->assign('ordersStatuses', $orders->statuses);
        $app->tpl->assign(
            'orders',
            $orders->getOrders(
                [
                    'user' => $profile->id,
                    'status' => ['rejected', 'closed', 'deleted'],
                ]
            )
        );
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    'GET',
    '/cart/order/?',
    function ($request, $response, $service, $app) use ($orders, $account) {
        $order_id = $orders->orderCreate();
        // sent email

        if ($order_id) {
            $mailer = new Mailer();
            $subject = 'Заказ создан - Odisey.ru';
            $template = 'email/email-order-created.tpl';
            $payload = ['order' => ['id' => $order_id, 'items' => $orders->getOrderItems($order_id)]];


            foreach (Configuration::MAIL_NOTIFY as $email) {
                $mailer->addBCC($email, 'Сотрудник компании Одиссей');
            }

            if ($mailer->compose(
                $subject,
                $template,
                $payload,
                [$account->getAccount()->email]
            )) {
                $response->redirect('/my/ordered/'.$order_id);
            };
        }
    }
);

$this->respond(
    'GET',
    '/ordered/[i:id]/?',
    function ($request, $response, $service, $app) use ($orders, $account) {
        $app->site->template = 'pages/my-ordered';
        $app->tpl->assign('order', $request->id);
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);

$this->respond(
    ['GET', 'POST'],
    '/profile/?',
    function ($request, $response, $service, $app) use ($account) {
        if ($request->method('post')) {
            if (isset($request->password) && $request->password !== $request->password2) {
                $app->tpl->assign('passworderror', true);
            } else {
                $account->updateProfile($request->identifier, $request->password);
                $app->tpl->assign('profileupdated', true);
            }
        }
        $app->site->template = 'pages/my-profile';
        $app->tpl->display('layouts/frontend-default.tpl');
    }
);
