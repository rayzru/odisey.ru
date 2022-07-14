<?php

namespace odissey;

$promo = new PromoController();
$catalog = new CatalogController();

use \Klein\Response;
use \Klein\Request;


$this->respond('GET', '/?', function (Request $request, Response $response, $service, $app) use ($promo) {
    $app->tpl->assign('promo', $promo->getPromo(['active' => 1]));
    $app->site->setCanonical('/promo');
    $app->site->template = 'pages/promos';
    $app->tpl->display('layouts/frontend-default.tpl');
});

$this->respond('GET', '/[i:id]-[:slug]/?', function (Request $request, Response $response, $service, $app) use ($promo, $catalog) {
    $p = $promo->getPromo(['id' => $request->id]);
    $app->site->addScript('//yastatic.net/share2/share.js');
    $uri = '/promo/' . $request->id . '-' . $p['slug'];
    if ($p['slug'] !== $request->slug) {
        $response->redirect($uri);
    }
    $app->tpl->assign('promo', $p);
    $app->tpl->assign('stocks', $catalog->getStockStrings());
    $app->site->addKeywords($promo->getPromoKeywords($request->id));
    $app->site->setTitle($p['seo_title']);
    $app->site->setCanonical($uri);
    $app->site->setDescription($p['seo_description']);
    $app->site->template = 'pages/promo';
    $app->tpl->display('layouts/frontend-default.tpl');
});
