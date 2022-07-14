<?php

namespace odissey;

$account = new AccountController();
$statsController = new StatsController();

$this->respond('POST', '/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$params = json_decode(base64_decode($request->param('d')));
	$params->ipv4 = Helpers::getClientIP();
	$params->session = session_id();
	$params->user_id = $account->isLogged() ? $account->getAccount()->id : null;
	$statsController->store($params);
	die();
});

$this->respond('GET', '/live/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$stats = $statsController->getLive();
	$response->json($stats);
	die();
});

$this->respond('GET', '/sessions/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$stats = $statsController->getActiveSessions();
	$response->json($stats);
	die();
});

$this->respond('GET', '/day/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$stats = $statsController->getStats(date($statsController::DATE_FORMAT_0), null, $statsController::GROUPBY_HOUR);
	$response->json($stats);
	die();
});

$this->respond('GET', '/week/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$stats = $statsController->getStats(
		date($statsController::DATE_FORMAT_0, strtotime('-7 days')),
		null,
		$statsController::GROUPBY_DAY
	);
	$response->json($stats);
	die();
});

$this->respond('GET', '/month/?', function ($request, $response, $service, $app) use ($account, $statsController) {
	$stats = $statsController->getStats(
		date($statsController::DATE_FORMAT_0, strtotime('-1 month')),
		null,
		$statsController::GROUPBY_DAY
	);
	$response->json($stats);
	die();
});
