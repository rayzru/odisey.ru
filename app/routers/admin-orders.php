<?php

namespace odissey;

$orders = new OrdersController();

$this->respond('GET', '/orders/?', function ($request, $response, $service, $app) use ($orders) {

	$app->site->addScript("/assets/js/admin-orders.js");

	$app->site->template = 'admin/admin-orders';
	$page = $request->param('page') ? abs($request->param('page')) : 1;
	$query = $request->param('query');
	$filter_status = $request->param('status');
	$items = $orders->getOrders(['page' => $page, 'query' => $query, 'status' => $filter_status]);

	if ($items['count'] > 0 && $items['pages'] < $page) {
		$response->redirect(
			'?page=' . $items['pages']
			. (!is_null($query) ? '&query=' . $items['query'] : '')
			. (is_array($filter_status) && count($filter_status) ? '&status[]=' .
				implode("&status[]=", $filter_status) : '')
		)->send();
	}

	$app->tpl->assign('order_statuses', $orders->statuses);
	if ($filter_status && count($filter_status)) {
		$app->tpl->assign('filter_statuses', array_flip($filter_status));
	}
	$app->tpl->assign('order_labels', $orders->labels);
	$app->tpl->assign('statuses_count', $orders->getOrderStatusesCounts());
	$app->tpl->assign('orders', $items);
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond('GET', '/orders/[i:id]/?', function ($request, $response, $service, $app) use ($orders) {

	$app->site->addScript("/assets/js/admin-orders.js");
	$app->site->template = 'admin/admin-order';
	$order = $orders->getOrders(['id' => $request->id]);
	$breadcrumbs = [];
	$breadcrumbs[] = ['title' => 'Заказы', 'url' => '/admin/orders'];
	$breadcrumbs[] = ['title' => $request->id, 'url' => ''];

	$app->tpl->assign('order', $order);
	$app->tpl->assign('breadcrumbs', $breadcrumbs);
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond('POST', '/orders/[i:id]/[added|queued|rejected|closed|deleted:status]/?',
	function ($request, $response, $service, $app) use ($orders) {
		$response->json(['success' => $orders->setStatus($request->id, $request->status)]);
	}
);

$this->respond('GET', '/orders/[i:id]/details/?', function ($request, $response, $service, $app) use ($orders) {
	//
});
