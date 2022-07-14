<?php
namespace odissey;

$catalog = new CatalogController();

$this->respond('GET', '/features/?', function ($request, $response, $service, $app) use ($catalog) {
	$app->site->addScript('/assets/js/admin-features.js');
	$app->site->template = 'admin/admin-features';
	$page = $request->param('page') ? abs($request->param('page')) : 1;
	$query = $request->param('query');
	$features = $catalog->getFeatures(['page' => $page, 'query' => $query, 'usage' => true]);
	if ($features['count'] > 0 && $features['pages'] < $page) {
		$response
			->redirect('?page=' . $features['pages'] . (!is_null($query) ? '&query=' . $features['query'] : ''))
			->send();
	}
	$app->tpl->assign('features', $features);
	$app->tpl->assign('featuresTypes', $catalog->getFeaturesTypes());
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond(['GET', 'POST'], '/features/[i:id]/?', function ($request, $response, $service, $app) use ($catalog) {
	$app->site->addScript('/assets/js/admin-features.js');
	if ($request->method('POST')) {
		$factory = new Validation();
		$rules = array(
			'title' => ['required', 'max:255'],
			'unit' => ['max:120']
		);
		$messages = array(
			'title.required' => 'Обязательное поле',
			'title.max' => 'Максимальная длина 255 символов',
			'unit.max' => 'Максимальная длина 120 символов'
		);

		$validator = $factory->make($request->params(), $rules, $messages);

		if ($validator->fails()) {
			$app->tpl->assign('errors', $validator->messages()->messages());
			$app->tpl->assign('feature', $request->params());
		} else {
			if ($catalog->updateFeature($request->id, $request->params())) {
				$response->redirect('/admin/features/')->send();
			};
		}
	}

	$app->tpl->assign('feature', $catalog->getFeature($request->id));
	$app->tpl->assign('featuresTypes', $catalog->getFeaturesTypes());
	$app->site->template = 'admin/admin-feature-form';
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond(['GET', 'POST'], '/features/add/?', function ($request, $response, $service, $app) use ($catalog) {

	if ($request->method('POST')) {
		$factory = new Validation();
		$rules = array(
			'title' => ['required', 'max:255'],
			'unit' => ['max:120']
		);
		$messages = array(
			'title.required' => 'Обязательное поле',
			'title.max' => 'Максимальная длина 255 символов',
			'unit.max' => 'Максимальная длина 120 символов'
		);

		$validator = $factory->make($request->params(), $rules, $messages);

		if ($validator->fails()) {
			$app->tpl->assign('errors', $validator->messages()->messages());
			$app->tpl->assign('feature', $request->params());
		} else {
			if ($catalog->addFeature($request->params())) {
				$response->redirect('/admin/features/')->send();
			};
		}
	}

	$app->tpl->assign('featuresTypes', $catalog->getFeaturesTypes());
	$app->site->template = 'admin/admin-feature-form';
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond('DELETE', '/features/[i:id]/?', function ($request, $response, $service, $app) use ($catalog) {
	$response->json($catalog->deleteFeature($request->id));
});
