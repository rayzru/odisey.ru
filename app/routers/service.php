<?php
namespace odissey;

$this->respond('GET', '/?', function ($request, $response, $service, $app) {
	$app->site->template = 'pages/service';
	$app->site->addScript("/assets/js/jivosite.js", false);
	$app->tpl->display('layouts/frontend-default.tpl');
});

$this->respond('GET', '/[center|delivery|projects:page]/?', function ($request, $response, $service, $app) {
    $app->site->template = 'pages/service-' . $request->page;
    $app->site->addScript("/assets/js/jivosite.js", false);
    $app->tpl->display('layouts/frontend-default.tpl');
});
