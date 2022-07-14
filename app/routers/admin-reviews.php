<?php

namespace odissey;

$reviews = new ReviewsController();

$this->respond('GET', '/reviews/?', function ($request, $response, $service, $app) use ($reviews) {

	$app->site->addScript("/assets/js/admin-reviews.js");

	$app->site->template = 'admin/admin-reviews';
	$page = $request->param('page') ? abs($request->param('page')) : 1;
	$query = $request->param('query');
	$filter_status = $request->param('status');
	$items = $reviews->getReviews(['page' => $page, 'query' => $query, 'status' => $filter_status]);
	if (count($items['items']) > 0 && $items['pages'] < $page) {
		$response->redirect(
			'?page=' . $items['pages'] .
			(!is_null($query) ? '&query=' . $items['query'] : '') .
			(is_array($filter_status) && count($filter_status) ? '&status[]=' .
				implode("&status[]=", $filter_status) : '')
		)->send();
	}

	if ($filter_status && count($filter_status)) {
		$app->tpl->assign('filter_statuses', array_flip($filter_status));
	}

	$app->tpl->assign('review_statuses', $reviews->statuses);
	$app->tpl->assign('review_labels', $reviews->labels);

	$app->tpl->assign('statuses_count', $reviews->getReviewStatusesCounts());
	$app->tpl->assign('reviews', $items);
	$app->tpl->display('layouts/admin-default.tpl');
});

$this->respond('GET', '/reviews/[i:id]/?', function ($request, $response, $service, $app) use ($reviews) {

	$app->site->addScript("/assets/js/admin-reviews.js");

	$app->site->template = 'admin/admin-review';

	$review = $reviews->getReviews(['id' => $request->id]);

	$breadcrumbs = [];

	$breadcrumbs[] = ['title' => 'Отзывы', 'url' => '/admin/reviews'];
	$breadcrumbs[] = ['title' => $request->id, 'url' => ''];

	$app->tpl->assign('review', $review);

	$app->tpl->assign('review_statuses', $reviews->statuses);
	$app->tpl->assign('review_labels', $reviews->labels);

	$app->tpl->assign('breadcrumbs', $breadcrumbs);
	$app->tpl->display('layouts/admin-default.tpl');
});


$this->respond('POST', '/reviews/[i:id]/ajax?',
	function ($request, $response) use ($reviews) {
		$response->json(['success' => $reviews->updateReview($request->id, $request->review)]);
	}
);

$this->respond('POST', '/reviews/[i:id]/[moderated|published|rejected|deleted:status]/?',
	function ($request, $response) use ($reviews) {
		$response->json(['success' => $reviews->setStatus($request->id, $request->status)]);
	}
);

