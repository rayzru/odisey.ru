<?php

namespace odissey;

$account = new AccountController();
$catalog = new CatalogController();
$orders = new OrdersController();
$reviews = new ReviewsController();
$callbacks = new CallbackController();
$promo = new PromoController();

use Klein\Request;
use Klein\Response;

$this->respond(
    '/[a:controller]/?[a:action]?/[i:id]?',
    function ($request, $response, $service, $app) {
        $app->tpl->assign('controller', $request->controller);
    }
);

$this->respond(
    function (Request $request, Response $response, $service, $app) use ($account, $orders, $reviews, $callbacks, $promo) {
        if ((!$account->isLogged() || !$account->isAdmin()) && $request->uri() !== '/admin') {
            $response->redirect('/admin')->send();
        }

        if ($request->paramsGet()->exists('logout')) {
            $response->redirect('/admin')->send();
        }
        $app->site->addScript('/assets/js/admin.js');
        $app->tpl->assign('ordersCount', $orders->getOrdersCount());
        $app->tpl->assign('reviewsCount', $reviews->getReviewsCount());
        $app->tpl->assign('callbacksCount', $callbacks->getCallbacksCount());
        $app->tpl->assign('promoCount', $promo->getPromoCount());
    }
);

$this->respond(
    ['GET', 'POST'],
    '/?',
    function (Request $request, Response $response, $service, $app) use ($account, $catalog) {
        if ($request->method() === 'POST') {
            if ($account->authEmail($request->email, $request->password, Account::ACCOUNT_ADMIN)) {
                $data = $account->getAccountByEmail($request->email);
                $account->setAccount($data);
            } else {
                $app->tpl->assign('email', $request->email);
                $app->tpl->assign('password', $request->password);
                $app->tpl->assign(
                    'error',
                    '<h4>Ошибка авторизации</h4>Логин или пароль указаны не верно.'
                );
            }
        }

        $app->tpl->assign('ip', Helpers::getClientIP());

        if ($account->isLogged() && $account->isAdmin()) {
            $app->site->addScript('/vendor/moment/moment/min/moment.min.js');
            $app->site->addScript('/vendor/moment/moment/locale/ru.js');
            $app->site->addScript('/vendor/nnnick/chartjs/dist/Chart.min.js');
            $app->site->addScript('/assets/js/admin-dashboard.js');

            $app->tpl->assign('itemsNew', $catalog->getItems(['new' => true]));
            $app->tpl->assign(
                'itemsSpecial',
                $catalog->getItems(['special' => true])
            );
            $app->tpl->assign(
                'itemsBestsellers',
                $catalog->getItems(['top' => true])
            );
            $app->tpl->assign(
                'itemsCommission',
                $catalog->getItems(['commission' => true])
            );

            $app->site->template = 'admin/admin-dashboard';
            $app->tpl->display('layouts/admin-default.tpl');
        } else {
            $app->site->template = 'admin/admin-auth';
            $app->tpl->display('layouts/admin-blank.tpl');
        }
    }
);

include 'admin-catalog.php';
include 'admin-orders.php';
include 'admin-reviews.php';
include 'admin-users.php';
include 'admin-content.php';
include 'admin-features.php';
include 'admin-service.php';
include 'admin-callbacks.php';
include 'admin-promo.php';
