<?php
namespace odissey;

$users = new UsersController();

$this->respond('GET', '/dictionary/?', function ($request, $response, $service, $app) use ($users) {
	$app->site->template = 'admin/admin-dictionary';
	$app->tpl->assign('dictionary', $users);
	$app->tpl->display('layouts/admin-default.tpl');
});
