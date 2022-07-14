<?php

namespace odissey;

$this->respond(
    'GET',
    '/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'pages/contacts';
        $app->site->setTitle("Контакты - как найти Одиссей?");
        $app->site->setDescription("Карты, телефоны, мессенджеры, форма обратной связи с компанией Одиссей");
        $app->site->addScript(
            'https://maps.googleapis.com/maps/api/js?v=3.exp&hl=ru&libraries=places&key='.Configuration::GOOGLEAPI_MAPS_KEY,
            true
        );
        $app->site->addScript('https://www.google.com/recaptcha/api.js?hl=ru&onload=onloadCallback&render=explicit', true, true, true);
        $app->site->addScript('/assets/js/+pages/contacts.js', true);
        $app->site->addScript("/assets/js/jivosite.js", false);

        $app->tpl->display('layouts/frontend-withoutsearch.tpl');
    }
);

$this->respond(
    'POST',
    '/feedback/?',
    function ($request, $response, $service, $app) {

        header('Content-type:application/json;charset=utf-8');

        $factory = new Validation();

        $factory->extend(
            'captcha_valid',
            function ($attribute, $value, $parameters) use ($request) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $secret = Configuration::GOOGLEAPI_RECAPTCHA_SECRETKEY;
                $url = 'https://www.google.com/recaptcha/api/siteverify';
                $res = file_get_contents($url."?secret=".$secret."&response=".$value."&remoteip=".$ip);
                $res = json_decode($res, true);

                return $res['success'];
            },
            'You are robot, buddy!'
        );

        $factory->extend(
            'has_url',
            function ($attribute, $value, $parameters) use ($request) {
                $pattern = '#(www\.|https?://)?[a-z0-9]+\.[a-z0-9]{2,4}\S*#i';
                preg_match_all($pattern, $value, $matches, PREG_PATTERN_ORDER);

                return count($matches) !== 0;
            },
            'Text has URLs in it'
        );

        $rules = [
            'email' => ['required', 'email'],
            'name' => ['required', 'min:2', 'max:100'],
            'message' => ['required', 'min:20', 'max:2000', 'has_url'],
            'g-recaptcha-response' => ['required', 'captcha_valid'],
        ];

        $messages = [
            'email.required' => 'Обязательное поле',
            'email.email' => 'Некорректный Email',
            'name.required' => 'Обязательное поле',
            'name.min' => 'Подразумевается, что вы укажите, как к Вам обращаться',
            'name.max' => 'Шутите! Такого имени не бывает',
            'message.required' => 'Обязательное поле',
            'message.min' => 'Слижком короткое сообщение',
            'message.max' => 'Лимит букв исчерпан',
            'message.has_url' => 'В тексте вашего сообщения обнаружены ссылки. Плохо.',
            'g-recaptcha-response.captcha_valid' => 'Роботам вход воспрещен',
            'g-recaptcha-response.required' => 'Проверка на человечность обязательна',
        ];

        $validator = $factory->make($request->params(), $rules, $messages);

        if (!$validator->fails()) {
            $mailer = new Mailer();
            $subject = '[odisey.ru] Новое сообщение с сайта';
            $mailer->setFrom($request->email, $request->name);
            $mailer->setSubject($subject);
            //$mailer->addAddress($subject);
            foreach (Configuration::MAIL_NOTIFY as $email) {
                $mailer->addAddress($email, 'Сотрудник компании Одиссей');
            }
            $app->tpl->assign(
                'feedback',
                [
                    'name' => htmlspecialchars($request->name),
                    'email' => $request->email,
                    'phone' => htmlspecialchars($request->phone),
                    'message' => htmlspecialchars($request->message),
                ]
            );

            $message = $app->tpl->fetch('email/email-feedback.tpl');
            $emailTemplateValues = [
                'title' => $subject,
                'domain' => $_SERVER['HTTP_HOST'],
                'content' => $message,
            ];
            $app->tpl->assign('emailTemplate', $emailTemplateValues);
            $template = $app->tpl->fetch('email/email.tpl');
            $mailer->setMessage($template);
            if ($mailer->send()) {
                $response->json(['success' => true]);
                // echo json_encode();
            } else {
                $response->json(
                    [
                        'success' => false,
                        'errors' => ['Возникли проблемы при отсылке почтового сообщения'],
                    ]
                );
            }
        } else {
            $response->json(
                [
                    'success' => false,
                    'errors' => $validator->messages()->messages(),
                ]
            );
        }
    }
);
