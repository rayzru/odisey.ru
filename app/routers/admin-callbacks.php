<?php

namespace odissey;

$callbacks = new CallbackController();

$this->respond(
    'GET',
    '/callbacks/?',
    function (\Klein\Request $request, \Klein\Response $response, $service, $app) use ($callbacks) {

        $app->site->addScript("/assets/js/admin-callbacks.js");

        $app->site->template = 'admin/admin-callbacks';
        $page = $request->param('page') ? abs($request->param('page')) : 1;
        $query = $request->param('query');
        $filter_status = $request->param('status');
        $items = $callbacks->getCallbacks(
            ['page' => $page, 'query' => $query, 'status' => $filter_status]
        );

        if ($items['count'] > 0 && $items['pages'] < $page) {
            $response->redirect(
                '?page='.$items['pages']
                .($query !== null ? '&query='.$items['query'] : '')
                .(is_array($filter_status) && count($filter_status) ? '&status[]='
                    .implode("&status[]=", $filter_status) : '')
            )->send();
        }

        $app->tpl->assign('callback_statuses', $callbacks->statuses);
        if ($filter_status && count($filter_status)) {
            $app->tpl->assign('filter_statuses', array_flip($filter_status));
        }
        $app->tpl->assign('callback_labels', $callbacks->labels);
        $app->tpl->assign(
            'statuses_count',
            $callbacks->getCallbackStatusesCounts()
        );
        $app->tpl->assign('callbacks', $items);
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'POST',
    '/callbacks/[i:id]/[accepted|dismissed:status]/?',
    function ($request, \Klein\Response $response) use ($callbacks) {
        $response->json(
            ['success' => $callbacks->setStatus($request->id, $request->status)]
        );
    }
);
