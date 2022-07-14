<?php

namespace odissey;

use Smarty;

require_once __DIR__.'/init.php';

// SENTRY.io
if ($_SERVER['HTTP_HOST'] === 'odisey.ru') {
    $sCl = new \Raven_Client('https://@sentry.io/291126');
    $error_handler = new \Raven_ErrorHandler($sCl);
    $error_handler->registerExceptionHandler();
    $error_handler->registerErrorHandler();
    $error_handler->registerShutdownFunction();
}

session_start();

$klein = new \Klein\Klein();
$account = new AccountController();
$catalog = new CatalogController();
$content = new ContentController();
$orders = new OrdersController();

$klein->respond(
    function ($request, $response, $service, $app) use ($klein, $catalog, $account, $orders) {

        if ($request->paramsGet()->exists('logout')) {
            $account->logout();
            $request->param(['logout' => null]);
        }

        $app->site = new Site();
        $app->tpl = new Smarty();

        $app->tpl->template_dir = Configuration::TEMPLATE_DIR;
        $app->tpl->compile_dir = Configuration::TEMPLATE_CACHE;
        $app->tpl->cache_dir = Configuration::TEMPLATE_CACHE;
        $app->tpl->compile_id = 'frontend';
        $app->tpl->caching = 0;
        $app->tpl->debugging = Configuration::DEBUG;
        $app->tpl->plugins_dir[] = 'app/addons/smarty-plugins';

        $app->tpl->assign('domain', $_SERVER['SERVER_NAME']);

        // jquery
        $app->site->addScript("https://code.jquery.com/jquery-3.3.1.min.js", true);
        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js");
        $app->site->addScript("https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js");
        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.2.1/typeahead.bundle.min.js");

        if (0 !== strpos($request->pathname(), '/admin')) {
            // FRONTEND
            $app->site->addStyle("https://fonts.googleapis.com/css?family=Exo+2:500|Open+Sans:400,600&amp;subset=cyrillic");
            // $app->site->addStyle("https://fonts.googleapis.com/css?family=Roboto+Slab:400,700&amp;subset=cyrillic");
            $app->site->addStyle('/assets/styles/css/index.css');

            $app->site->addScript("/assets/js/common-scripts.min.js");

            $app->site->addScript("/vendor/inputmask/min/jquery.inputmask.bundle.min.js");

            $app->site->addScript("/assets/js/t.min.js");

            $app->tpl->assign('menucat', $catalog->getCategories(['parent' => 0]));
            $app->tpl->assign('cartCount', $orders->getCartCount());
            $app->tpl->assign('catalogItems', $catalog->totalItems());
        } else {
            // ADMIN PANEL
            $app->site->addStyle("/components/font-awesome/font-awesome-built.css");
            $app->site->addStyle('/assets/styles/css/admin-index.css');
        }

        $app->tpl->assign('site', $app->site);
        $app->tpl->assign('account', $account->getAccount());

        if ($account->isLogged()) {
            //$orders = new
            //$app->tpl->assign('cartItems', $orders->getCartItems());
        }
    }
);

$klein->respond(
    'GET',
    '/logout/?',
    function ($request, $response, $service, $app) use ($account) {
        $account->logout();
        $response->redirect('/my');
    }
);

$klein->respond(
    'GET',
    '/?',
    function ($request, $response, $service, $app) use ($account, $catalog, $content) {

        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js");
        $app->site->addScript("/assets/js/+pages/mainpage.js");

        $app->tpl->assign('popularCategories', $catalog->getPopularCategories());
        $topItems = array_merge(
            $catalog->getItems(['new' => true]),
            $catalog->getItems(['special' => true]),
            $catalog->getItems(['top' => true])
        );
        $app->tpl->assign('topItems', $topItems);

        $app->tpl->assign('news', $content->getList(['type' => 'news', 'limit' => 5, 'active' => 1]));
        $app->tpl->assign('articles', $content->getList(['type' => 'article', 'limit' => 5, 'active' => 1]));

        $app->tpl->assign(
            'logos',
            [
                'abb.jpg',
                'acc.jpg',
                'alco.jpg',
                'alternativa.png',
                'ariada.png',
                'atlant.png',
                'autospecoborudovanie.png',
                'bfai.png',
                'birusa.jpg',
                'bitzer.jpg',
                'capeland.jpg',
                'cas.png',
                'chuvashorgtehnika.png',
                'chvmz.png',
                'cp.jpg',
                'danfoss.png',
                'derby.jpg',
                'eliwell.jpg',
                'elkop.png',
                'embraco.jpg',
                'fabrika.jpg',
                'fimar.jpg',
                'frascold.jpg',
                'frostor.jpg',
                'fucsh.jpg',
                'grillmaster.png',
                'harris.jpg',
                'hicold.jpg',
                'kamenskvolokno.jpg',
                'karcher.png',
                'karyer.jpg',
                'kocateq.png',
                'lu-ve.jpg',
                'lunite.jpg',
                'maneurop.jpg',
                'mash.jpg',
                'massak.png',
                'midl.jpg',
                'mobil.jpg',
                'mtd.png',
                'mxm.jpg',
                'nevskie.jpg',
                'nordika.jpg',
                'ottokurtbach.png',
                'pfaff.png',
                'polair.png',
                'polys.jpg',
                'premier.jpg',
                'rada.jpg',
                'rational.jpg',
                'robotcoupe.png',
                'shtrihm.png',
                'sikom.jpg',
                'snezh.jpg',
                'starfood.jpg',
                'starmix.png',
                'stihl.png',
                'sungaden.png',
                'termofor.png',
                'thermaflex.jpg',
                'total.jpg',
                'unox.jpg',
                'uralasbest.png',
                'vati.png',
                'voskhod.jpg',
                'yarstroy.png',
                'zarges.png',
            ]
        );

        $mainpageKeywords = [
            'Холодильное',
            'Пищевое',
            'Складское',
            'Торговое',
            'Упаковочное',
            'Фермерское',
            'Стремянки',
            'вышки-туры',
            'строительные леса',
            'Теплообогреватели',
            'РТИ',
            'Пластики',
            'Электроизоляционные',
            'Компрессоры',
            'пневмооборудование',
            'Вибротехника',
            'Производство алкогольной продукции',
        ];

        $app->site->template = 'pages/index';
        $app->site->title = 'Одиссей - поставщик оборудования';
        $app->site->description = 'Продукция производственно-технического назначения: холодильное, пищевое,'.
            'торговое, складское, упаковочное, фермерское. Продажа, доставка, обслуживание.';
        $app->site->addKeywords($mainpageKeywords);
        $app->tpl->display('layouts/frontend-index.tpl');
    }
);

$klein->respond(
    '/[a:controller]/?[**]',
    function ($request, $response, $service, $app) {
        $app->tpl->assign('controller', $request->controller);
    }
);

foreach (Application::$namespaces as $handler) {
    $klein->with("/{$handler}/?", __DIR__."/routers/{$handler}.php");
}

$klein->onHttpError(
    function ($code, $router) {
        $app = $router->app();
        if ($code >= 400 && $code < 500) {
            header('HTTP/1.1 '.$code);
            switch ($code) {
                case 404:
                    $app->site->template = 'pages/404';
                    break;
                default:
                    $app->site->template = 'pages/error';
                    $app->tpl->assign('code', $code);
                    break;
            }
            $app->tpl->display('layouts/frontend-catalog.tpl');
        } elseif ($code >= 500 && $code <= 599) {
            header('HTTP/1.1 '.$code);
            $app->site->template = 'pages/error';
            $app->tpl->assign('code', $code);
            $app->tpl->display('layouts/frontend-catalog.tpl');
        }
    }
);

try {
    $klein->dispatch();
} catch (\Klein\Exceptions\UnhandledException $exception) {
    // $exception->getPrevious();
    throw $exception;
    // $klein->response()->code(503);
    // return $klein->response()->chunk($exception->getMessage());
}
