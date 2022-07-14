<?php

namespace odissey;

$callback = new CallbackController();
$this->respond(
    'POST',
    '/?',
    function ($request, $response, $service, $app) use ($callback) {
        $data = [
            'phone' => $request->phone,
            'name' => $request->title ?? '',
            'comment' => $request->time,
        ];
        $sent = false;

        $added = $callback->add($data);
        if ($added) {
            $cb = $callback->getCallbacks(['id' => $added]);
            $mailer = new Mailer();

            $app->tpl->assign('callback', $data);
            $sent = $mailer->compose(
                'Обратный звонок с сайта Odisey.ru',
                'email/email-callback.tpl',
                ['callback' => $cb],
                Configuration::MAIL_NOTIFY
            );
        }

        $response->json(['success' => $sent && $added]);
    }
);
