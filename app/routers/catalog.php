<?php

namespace odissey;

$catalog = new CatalogController();
$account = new AccountController();
$content = new ContentController();
$reviews = new ReviewsController();

$breadcrumbs = [];
$breadcrumbs[] = ['title' => 'Каталог', 'url' => '/catalog'];

$this->respond(
    function ($request, $response, $service, $app) {
        $app->site->addScript("/assets/js/jivosite.js", false);
    }
);

$this->respond(
    'GET',
    '/?',
    function ($request, $response, $service, $app) use ($catalog) {
        // $app->tpl->assign('categories', $catalog->getCategories());

        $app->site->title = "Каталог";

        $app->site->template = 'pages/catalog-index';
        $app->site->setCanonical('/catalog');
        $app->tpl->assign('categories', $catalog->getCategoriesTree(0, true));
        $app->tpl->display('layouts/frontend-catalog.tpl');
    }
);

$this->respond(
    'GET',
    '/compare/[:ids]/?',
    function ($request, $response, $service, $app) use ($catalog) {
        // $app->tpl->assign('categories', $catalog->getCategories());
        $app->site->title = "Сравнение";

        $ids = explode(',', $request->ids);

        $items = $catalog->getItems(['id' => $ids]);
        if (count($items)) {
            $app->tpl->assign('items', $items);
            $app->tpl->assign('category', $catalog->getCategory($items[0]['category_id']));
            $app->tpl->assign('featuresData', $catalog->getFeaturesArray());
            $app->tpl->assign('features', $catalog->getItemsFeatures($ids));
        }
        $app->site->template = 'pages/catalog-compare';
        $app->tpl->display('layouts/frontend-catalog.tpl');
    }
);

/**
 *  Comission
 */
$this->respond(
    'GET',
    '/commission/?',
    function ($request, $response, $service, $app) use ($catalog, $content, $breadcrumbs) {

        $app->tpl->assign('content', $content->getCategoryContent($request->id));
        $app->tpl->assign('stocks', $catalog->getStockStrings());
        $category = [
            'title' => "Комиссионные товары",
            'seo_description' => '',
            'description' => '',
        ];
        $app->site->setTitle($category['title']);
        $app->site->setDescription($category['seo_description']);

        $order = null;

        $dir = null;

        if (preg_match("/-?(".implode('|', $catalog->getOrderKeys()).")/", $request->order, $matches)) {
            $order = count($matches) ? $matches[1] : null;
            $dir = count($matches) && $matches[0] === $matches[1] ? null : '-';
        };

        $items = $catalog->getItems(
            [
                'category' => $request->id,
                'features' => true,
                'active' => true,
                'order' => $order,
                'commission' => true,
                'dir' => $dir,
            ]
        );

        $max = 0;

        foreach ($items as $item) {
            $max = max($max, (float)$item['price']);
        }

        $app->site->setCanonical('/catalog/commission');

        $app->tpl->assign('category', $category);
        $app->tpl->assign('items', $items);
        $app->tpl->assign('itemsorder', $order);
        $app->tpl->assign('itemsorderdir', $dir);
        $app->tpl->assign('maxprice', ceil($max));
        $app->site->template = 'pages/catalog-items-icons';
        $app->site->addScript('/assets/js/vendor/ion-rangeslider/js/ion.rangeSlider.min.js');
        $app->site->addStyle('/assets/js/vendor/ion-rangeslider/css/ion.rangeSlider.css');
        $app->site->addStyle('/assets/js/vendor/ion-rangeslider/css/ion.rangeSlider.skinNice.css');
        $app->site->addScript('/assets/js/+pages/catalog-items.js');
        $app->tpl->display('layouts/frontend-catalog.tpl');
    }
);

/**
 *  Categories
 */
$this->respond(
    'GET',
    '/[i:id]-?[*:slug]?/?',
    function ($request, $response, $service, $app) use ($catalog, $content, $breadcrumbs) {
        // $app->tpl->assign('categories', $catalog->getCategories());
        $category = $catalog->getCategory($request->id, ['active' => true]);
        if (!empty($category)) {
            $slug = Helpers::getSlug($category['title']);
            if ($request->slug !== $slug) {
                $url = "/catalog/".$request->id."-".$slug;
                $response->redirect($url, 301);
            } else {
                $app->tpl->assign('content', $content->getCategoryContent($request->id));
                $app->tpl->assign('stocks', $catalog->getStockStrings());
                $app->tpl->assign('category', $category);
                $title = !empty($category['seo_title']) ? $category['seo_title'] : $category['title'];
                $app->site->setTitle($title);
                $app->site->setDescription($category['seo_description']);
                $app->site->addKeywords($catalog->getCategoryKeywords($request->id));

                if ($category['filename']) {
                    $img = Helpers::getMediaCachePath($category['filename'], '500x500', true);
                    $app->site->setImage($img);
                }

                $path = $catalog->getPath($category['id']);
                foreach ($path as $p) {
                    if ($p['id'] != $request->id) {
                        $breadcrumbs[] = [
                            'title' => $p['title'],
                            'url' => '/catalog/'.Helpers::getCategorySlug($p['id'], $p['title']),
                        ];
                    }
                }
                $app->tpl->assign('breadcrumbs', $breadcrumbs);
                if ($category['is_leaf'] === 0) {
                    $app->tpl->assign('items', $catalog->getCategories(['parent' => $request->id, 'active' => 1]));
                    $app->site->template = 'pages/catalog-category';
                } else {
                    $order = null;

                    $dir = null;

                    if (preg_match("/-?(".implode('|', $catalog->getOrderKeys()).")/", $request->order, $matches)) {
                        $order = count($matches) ? $matches[1] : null;

                        $dir = count($matches) && $matches[0] === $matches[1] ? null : '-';
                    };

                    $items = $catalog->getItems(
                        [
                            'category' => $request->id,
                            'features' => true,
                            'active' => true,
                            'order' => $order,
                            'dir' => $dir,
                        ]
                    );
                    $max = 0;
                    foreach ($items as $item) {
                        $max = max($max, (float)$item['price']);
                    }
                    $app->tpl->assign('items', $items);
                    $app->tpl->assign('itemsorder', $order);
                    $app->tpl->assign('itemsorderdir', $dir);
                    $app->tpl->assign('maxprice', ceil($max));
                    $app->site->template = 'pages/catalog-items-'.$category['appearance'];

                    $app->site->addScript('/vendor/ion-rangeslider/js/ion.rangeSlider.min.js');
                    $app->site->addStyle('/vendor/ion-rangeslider/css/ion.rangeSlider.css');
                    $app->site->addStyle('/vendor/ion-rangeslider/css/ion.rangeSlider.skinNice.css');

                    $app->site->addScript('/assets/js/+pages/catalog-items.js');
                }
                $app->site->setCanonical(
                    implode(['/catalog/', Helpers::getCategorySlug($category['id'], $category['title'])])
                );
                $app->tpl->display('layouts/frontend-catalog.tpl');
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            $app->site->template = 'pages/404';
            $app->tpl->display('layouts/frontend-catalog.tpl');
        }
    }
);

$this->respond(
    'GET',
    '/p[i:id]-?[*:slug]?/?',
    function ($request, $response, $service, $app) use ($catalog, $breadcrumbs, $reviews) {
        $item = $catalog->getItem($request->id, ['active' => 1]);
        if (!empty($item)) {
            $slug = Helpers::getSlug($item['title']);
            if ($request->slug !== $slug) {
                $url = "/catalog/p".$request->id."-".$slug;
                $response->redirect($url, 301);
            } else {
                $title = !empty($item['seo_title']) ? $item['seo_title'] : $item['title'];
                $app->site->setTitle($title);
                $app->site->setDescription($item['seo_description']);
                $app->site->addKeywords($catalog->getItemKeywords($request->id));

                if (count($item['images'])) {
                    $img = Helpers::getMediaCachePath($item['images'][0]['filename'], '500x500', true);
                    $app->site->setImage($img);
                }

                $app->site->addScript('//yastatic.net/share2/share.js');

                $app->site->addScript('/vendor/lightbox2/js/lightbox.min.js');
                $app->site->addStyle('/vendor/lightbox2/css/lightbox.min.css');

                $app->site->addScript('/vendor/raty-js/jquery.raty.js');
                $app->site->addStyle('/vendor/raty-js/jquery.raty.css');

                if (count($item['related']) || count($item['similar'])) {
                    $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js");
                }


                $app->tpl->assign('item', $item);
                $app->tpl->assign('stock', $item['stock']);
                $app->tpl->assign('stocks', $catalog->getStockStrings());

                $path = $catalog->getPath($item['category_id']);

                foreach ($path as $p) {
                    $breadcrumbs[] = [
                        'title' => $p['title'],
                        'url' => '/catalog/'.Helpers::getCategorySlug($p['id'], $p['title']),
                    ];
                }
                $app->tpl->assign('breadcrumbs', $breadcrumbs);

                $app->tpl->assign('articles', $catalog->getItemArticles($request->id));

                $app->tpl->assign(
                    'reviews',
                    $reviews->getReviews(
                        [
                            'item_id' => $request->id,
                            'status' => 'published',
                            'pagerWidth' => 1000,
                            'limit' => 1000,
                        ]
                    )
                );

                $app->tpl->assign('userreview', $reviews->getUserItemReviews($request->id));


                $app->site->template = 'pages/catalog-item';
                $app->site->setCanonical(implode(['/catalog/', Helpers::getItemSlug($item['id'], $item['title'])]));
                $app->tpl->display('layouts/frontend-catalog.tpl');
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            $app->site->template = 'pages/404';
            $app->tpl->display('layouts/frontend-catalog.tpl');
        }
    }
);

$this->respond(
    'POST',
    '/p[i:id]-?[*:slug]?/reviews/ajax?',
    function ($request, $response) use ($catalog, $account, $reviews) {
        if ($catalog->hasItem($request->id) && $account->isLogged()) {
            $data = [
                'item_id' => $request->id,
                'review_id' => $request->review_id ?? 0,
                'status' => 'moderated',
                'user_id' => $account->getAccount()->id,
                'rating' => $request->score ?? 0,
                'anonymously' => $request->anonymously == 'on' ? 1 : 0,
                'review' => $request->review ?? '',
            ];
            if ($request->review_id) {
                return $response->json(['success' => $reviews->updateItemReview($data)]);
            } else {
                return $response->json(['success' => $reviews->addItemReview($data)]);
            }
        } else {
            header('HTTP/1.1 404 Not Found');

            return $response->json(['success' => false]);
        }
    }
);

$this->respond(
    'PUT',
    '/p[i:id]-?[*:slug]?/reviews/?',
    function ($request, $response) use ($catalog, $account) {
        if ($catalog->hasItem($request->id) && $account->isLogged()) {
            $data = [
                'id' => $request->review_id,
                'item_id' => $request->id,
                'approved' => 0,
                'user_id' => $account->getAccount()->id,
                'rating' => $request->score ?? 0,
                'review' => $request->review,
            ];

            return $response->json(['success' => $catalog->updateItemReview($data)]);
        } else {
            header('HTTP/1.1 404 Not Found');

            return $response->json(['success' => false]);
        }
    }
);
