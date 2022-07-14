<?php

namespace odissey;

use Klein\Request;
use Klein\Response;

$promo = new PromoController();

$this->respond(
    'GET',
    '/promo/?',
    function (Request $request, Response $response, $service, $app) use ($promo) {
        $app->site->addScript("/assets/js/admin-promo.js");
        $app->site->template = 'admin/admin-promo';
        $app->tpl->assign('promo', $promo->getPromo());
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    ['GET', 'POST'],
    '/promo/add/?',
    function (Request $request, Response $response, $service, $app) use ($promo) {

        $app->site->addScript("/assets/js/admin-promo-form.js");
        $app->site->template = 'admin/admin-promo-form';

        $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
        $app->site->addScript("/assets/js/tinymce.init.js");
        $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
        $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
        $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
        $app->site->addScript("/assets/js/admin-content-form.js");
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js');
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ru.min.js');
        $app->site->addStyle(
            'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.standalone.min.css'
        );

        $promoData = [];

        if ($request->method('GET')) {
            $promoData = [
                'date_start' => date('d.m.Y'),
                'date_end' => date('d.m.Y', strtotime(' + 7 days')),
            ];
        }

        if ($request->method('POST')) {
            $promoData = $request->params();
            $validator = $promo->validateData($promoData);
            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
            } else {
                $id = $promo->add($promoData);
                if ($id) {
                    $response->redirect("/admin/promo/".$id)->send();
                }
            }
        }

        $app->tpl->assign('promo', $promoData);

        $breadcrumbs = [];
        $breadcrumbs[] = ['title' => 'Акции', 'url' => '/admin/promo'];

        $app->tpl->assign('breadcrumbs', $breadcrumbs);
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    ['GET', 'POST'],
    '/promo/[i:id]/?',
    function (Request $request, $response, $service, $app) use ($promo) {

        $app->site->addScript("/assets/js/admin-promo-form.js");
        $app->site->template = 'admin/admin-promo-form';

        $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
        $app->site->addScript("/assets/js/tinymce.init.js");
        $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
        $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
        $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
        $app->site->addScript("/assets/js/admin-content-form.js");
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js');
        $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ru.min.js');
        $app->site->addStyle(
            'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.standalone.min.css'
        );


        if ($request->method('POST')) {
            $promoData = $request->params();
            $validator = $promo->validateData($promoData);
            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
            } else {
                $promo->update($request->id, $promoData);
                $promo->removeKeywords($request->id);
                if (isset($request->keywords)) {
                    $promo->addKeywords($request->id, $request->keywords);
                }

                $response->redirect("/admin/promo/")->send();
            }
        }


        $data = $promo->getPromo(['id' => $request->id]);

        $breadcrumbs = [];
        $breadcrumbs[] = ['title' => 'Акции', 'url' => '/admin/promo'];
        $breadcrumbs[] = ['title' => $request->id, 'url' => ''];

        $app->tpl->assign('promo', $data);
        $app->tpl->assign('breadcrumbs', $breadcrumbs);
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'DELETE',
    '/promo/[i:id]/[json:ajax]?',
    function (Request $request, Response $response) use ($promo) {
        $res = $promo->deletePromo($request->id);
        if ($request->ajax === 'json') {
            return $response->json(['success' => $res]);
        } else {
            $response->redirect('/promo')->send();
        }
    }
);


$this->respond(
    'POST',
    '/promo/addItem/?',
    function (Request $request, Response $response) use ($promo) {
        $data = $request->params();
        $res = $promo->addItem($request->params());

        return $response->json(
            ($res)
                ? ['success' => true, 'promo_id' => $data['promo_id'], 'item' => $res]
                : ['success' => false]
        );
    }
);


$this->respond(
    'DELETE',
    '/promo/[i:id]/item/[i:item]/?',
    function (Request $request, Response $response) use ($promo) {
        return $response->json(['success' => $promo->removeItem($request->id, $request->item)]);
    }
);
