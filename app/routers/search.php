<?php
namespace odissey;

use TeamTNT\TNTSearch\TNTSearch;

$tntconfig = [
	'driver' => 'mysql',
	'host' => Configuration::DATABASE_HOST,
	'database' => Configuration::DATABASE_DB,
	'username' => Configuration::DATABASE_USER,
	'password' => Configuration::DATABASE_PASS,
	'storage' => Configuration::SEARCH_CACHE,
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci'
];

$tnt = new TNTSearch;
$tnt->loadConfig($tntconfig);

$catalog = new CatalogController();

$this->respond(function ($request, $response, $service, $app) {
	$app->site->addScript("/assets/js/jivosite.js", false);
});

$this->respond('GET', '/?', function ($request, $response, $service, $app) use ($catalog, $tnt) {
	$app->site->title = "Поиск";
	$app->site->template = 'pages/search-index';

	$term = $request->param('term');
	if ($term) {
		$app->tpl->assign('searchQuery', $term);
		$app->site->title = $term;
		$app->site->template = 'pages/search-term';

		$tnt->asYouType = true;
		$tnt->fuzziness = true;
		$tnt->fuzzy_distance = 2;
        $tnt->fuzzy = [
            'prefix_length' => 2,
            'max_expansions' => 50,
            'distance' => 2
        ];

		$cleanTerm = Helpers::seoCleanup($term);

		$tnt->selectIndex("category.user");
		$search = $tnt->search($cleanTerm, 10);
		$searchCategories = (isset($search['ids']) && count($search['ids'])) ?
			$catalog->getCategories(['id' => $search['ids'], 'brief' => true]) : [];

		foreach ($searchCategories as $key => $item) {
			$searchCategories[$key]['slug'] = Helpers::getCategorySlug($item['id'], $item['title']);
		}

		$app->tpl->assign('searchCategories', $searchCategories);

		$tnt->selectIndex("items.user");
		$search = $tnt->search($cleanTerm, 100);
		$searchItems = (isset($search['ids']) && count($search['ids'])) ?
			$catalog->getItems(['id' => $search['ids'], 'brief' => true]) : [];
		foreach ($searchItems as $key => $item) {
			$searchItems[$key]['slug'] = Helpers::getItemSlug($item['id'], $item['title']);
		}

		$app->tpl->assign('searchItems', $searchItems);
	}

	$app->tpl->display('layouts/frontend-catalog.tpl');
});

$this->respond('GET', '/json/items/[*:query]/?', function ($request, $response, $service, $app) use ($catalog, $tnt) {
	header('Content-type:application/json;charset=utf-8');
    $isArticul = preg_match("/^[A-Z]{2}\d+/i", $request->query) > 0;

    $term = $isArticul ? trim($request->query) : Helpers::seoCleanup($request->query);

    if ($isArticul) {
        $search = $catalog->searchItemIdsByArticul($term);
        $search = array_map(function($el) { return $el['id']; }, $search);
        $result = (count($search)) ?
            $catalog->getItems(['id' => $search, 'brief' => true]) : [];
    } else {
        $tnt->asYouType = !$isArticul;
        $tnt->fuzziness = !$isArticul;
        $tnt->fuzzy_distance = $isArticul ? 0 : 2;
        // $tnt->fuzzy_max_expansions = 10;
        // $tnt->fuzzy_prefix_length = 0;
        $tnt->selectIndex($isArticul ? 'items-articul.user.nostem' : 'items.user');
        $search = $tnt->search($term, 20);

        $result = (isset($search['ids']) && count($search['ids'])) ?
            $catalog->getItems(['id' => $search['ids'], 'brief' => true]) : [];
    }

	foreach ($result as $key => $item) {
		$result[$key]['slug'] = Helpers::getItemSlug($item['id'], $item['title']);
	}
	echo json_encode($result);
	die();
});


$this->respond('GET', '/json/items.admin/[*:query]/?', function ($request) use ($catalog, $tnt) {
	header('Content-type:application/json;charset=utf-8');
    $isArticul = preg_match("/^[A-Z]{2}\d+/i", $request->query) > 0;

    $term = $isArticul ? trim($request->query) : Helpers::seoCleanup($request->query);

	if ($isArticul) {
        $search = $catalog->searchItemIdsByArticul($term);
        $search = array_map(function($el) { return $el['id']; }, $search);
        $result = (count($search)) ?
            $catalog->getItems(['id' => $search, 'brief' => true]) : [];
    } else {
        $tnt->asYouType = true;
        $tnt->selectIndex("items.admin");
        $tnt->fuzziness = true;
        $tnt->fuzzy_distance = 2;
        $term = Helpers::seoCleanup($request->query);
        $search = $tnt->search($term, 10);
        $result = (isset($search['ids']) && count($search['ids'])) ?
            $catalog->getItems(['id' => $search['ids'], 'brief' => true]) : [];
    }

    foreach ($result as $key => $item) {
        $result[$key]['slug'] = Helpers::getItemSlug($item['id'], $item['title']);
    }

	echo json_encode($result);
	die();
});

$this->respond('GET', '/json/categories/[*:query]?', function ($request) use ($catalog, $tnt) {
	header('Content-type:application/json;charset=utf-8');

	$tnt->selectIndex("category.user");
	$tnt->asYouType = true;
	$tnt->fuzziness = true;
	$tnt->fuzzy_distance = 2;
	$term = Helpers::seoCleanup($request->query);
	$search = $tnt->search($term, 10);
	$result = (isset($search['ids']) && count($search['ids'])) ?
		$catalog->getCategories(['id' => $search['ids'], 'brief' => true]) : [];
	foreach ($result as $key => $item) {
		$result[$key]['slug'] = Helpers::getCategorySlug($item['id'], $item['title']);
	}
	echo json_encode($result);
	die();
});

$this->respond('GET', '/json/categories.admin/[*:query]?', function ($request) use ($catalog, $tnt) {
	header('Content-type:application/json;charset=utf-8');
	$tnt->selectIndex("category.admin");
	$tnt->asYouType = true;
	$tnt->fuzziness = true;
	$tnt->fuzzy_distance = 2;
	$term = Helpers::seoCleanup($request->query);
	$search = $tnt->search($term, 10);
	$result = (isset($search['ids']) && count($search['ids'])) ?
		$catalog->getCategories(['id' => $search['ids'], 'brief' => true]) : [];
	foreach ($result as $key => $item) {
		$result[$key]['slug'] = Helpers::getCategorySlug($item['id'], $item['title']);
	}
	echo json_encode($result);
	die();
});
