<?php

namespace odissey;

$content = new ContentController();

$this->respond('GET', '/?', function ($request, $response, $service, $app) use ($content) {
    $app->tpl->assign('articles', $content->getList(['type' => [$content::CONTENT_ARTICLE], 'active' => 1, 'published' => 1]));
    $app->tpl->assign('news', $content->getList(['type' => [$content::CONTENT_NEWS], 'active' => 1, 'published' => 1]));
    $app->site->setCanonical('/feed/');
    $app->site->template = 'pages/feed-list';
    $app->tpl->display('layouts/frontend-default.tpl');
});

$this->respond('GET', '/[i:id]-[:slug]/?', function ($request, $response, $service, $app) use ($content) {
    $article = $content->get($request->id);
    $app->site->addScript('//yastatic.net/share2/share.js');
    $uri = '/feed/' . $request->id . '-' . $article['slug'];
    if ($article['slug'] !== $request->slug) {
        $response->redirect($uri);
    }
    $app->tpl->assign('article', $article);
    $app->site->addKeywords($content->getKeywords($request->id));
    $app->site->setTitle($article['title']);
    $app->site->setCanonical($uri);
    $app->site->setDescription($article['seo_description']);
    $app->site->template = 'pages/feed-article';
    $app->tpl->display('layouts/frontend-default.tpl');
});
