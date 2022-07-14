<?php

namespace odissey;

$content = new ContentController();

$this->respond(
    'GET',
    '/content/?',
    function ($request, $response, $service, $app) use ($content) {
        $app->site->template = 'admin/admin-content';
        $app->tpl->assign('content', $content->getList());
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    ['GET', 'POST'],
    '/content/[i:id]/?',
    function ($request, $response, $service, $app) use ($content) {

        if ($request->method('POST')) {
            $factory = new Validation();
            $rules = [
                'title' => ['required', 'max:255'],
                'type' => ['required', 'in:news,article,system,content'],
                'slug' => ['required', 'max:1000', 'regex:/[a-z0-9-_]+/'],
            ];
            $messages = [
                'title.required' => 'Обязательное поле',
                'title.max' => 'Максимальная длина 1000 символов',
                'type.required' => 'Обязательное поле',
                'type.in' => 'Возможно только одно из представленных значений: news, article, system, content',
                'slug.required' => 'Обязательное поле',
                'slug.max' => 'Максимальная длина 1000 символов',
                'slug.alpha_num' => 'Метка может состоять из символов латинского алфавита и цифр',
            ];
            $validator = $factory->make($request->params(), $rules, $messages);
            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                $app->tpl->assign('content', $request->params());
            } else {
                if ($request->id !== 'add') {
                    $id = $request->id;
                    $content->update($request->id, $request->params());
                    $content->removeKeywords($id);
                    $content->removeCategories($id);
                } else {
                    $id = $content->add($request->params());
                }

                if (isset($request->keywords)) {
                    $content->addKeywords($id, $request->keywords);
                }

                if (isset($request->categories)) {
                    $content->linkCategory($id, $request->categories);
                }

                $response->redirect("/admin/content")->send();
            }
        }

        $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
        $app->site->addScript("/assets/js/tinymce.init.js");
        $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
        $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
        $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
        $app->site->addScript("/assets/js/admin-content-form.js");
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js');
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ru.min.js');
        $app->site->addStyle('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.standalone.min.css');

        $breadcrumbs = [];
        $breadcrumbs[] = ['title' => 'Новости и статьи', 'url' => '/admin/content'];

        $app->tpl->assign('breadcrumbs', $breadcrumbs);

        if ($request->id != 'add') {
            $keywords = $content->getKeywords($request->id);
            $app->site->addKeywords($keywords);
            $app->tpl->assign('keywords', $keywords);
            $app->tpl->assign('categories', $content->getCatetoryLinks($request->id));
            $app->tpl->assign('content', $content->get($request->id));
        }

        $app->site->template = 'admin/admin-content-form';
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'DELETE',
    '/content/[i:id]/?',
    function ($request, $response, $service, $app) use ($content) {
        $response->json(
            [
                'status' => $content->delete($request->id),
            ]
        );
    }
);
