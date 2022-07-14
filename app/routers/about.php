<?php
namespace odissey;

$this->respond('GET', '/?', function ($request, $response, $service, $app) {
	$app->site->template = 'pages/about';
	$app->tpl->display('layouts/frontend-default.tpl');
});
