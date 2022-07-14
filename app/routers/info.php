<?php
namespace odissey;

$this->respond('GET', '/?', function ($request, $response, $service, $app) {
    $app->site->template = 'pages/info';
    $app->site->addScript("/assets/js/jivosite.js", false);
    $app->tpl->display('layouts/frontend-default.tpl');
});

$this->respond('GET', '/[credit|payment|guarantee|moneyback|certificates|privacy:page]/?', function ($request, $response, $service, $app) {
    $app->site->template = 'pages/info-' . $request->page;
    $app->site->addScript("/assets/js/jivosite.js", false);
    $app->tpl->display('layouts/frontend-default.tpl');
});
