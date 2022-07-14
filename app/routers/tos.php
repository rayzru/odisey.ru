<?php
namespace odissey;

$this->respond('GET', '/?', function ($request, $response, $service, $app) {
	$app->site->template = 'pages/tos';
	$app->site->addScript("/assets/js/jivosite.js", false);
	$app->tpl->display('layouts/frontend-default.tpl');
});
