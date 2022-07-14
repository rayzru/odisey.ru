<?php

namespace odissey;

class Configuration
{

    // Database
    const DATABASE_HOST = 'localhost';
    const DATABASE_DB = 'odissey_migrate';
    const DATABASE_USER = 'root';
    const DATABASE_PASS = '';

    const TEMPLATE_CACHE = 'tmp';
    const TEMPLATE_DIR = 'app/views';
    const TEMPLATE_ID = 'frontend';

    const GOOGLEAPI_MAPS_KEY = '';
    const GOOGLEAPI_RECAPTCHA_SITEKEY = '';
    const GOOGLEAPI_RECAPTCHA_SECRETKEY = '';

    const SEARCH_CACHE = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp';

    // Development purposes
    const DEBUG = false;

    const MAIL_FROM = 'noreply@odisey.ru';
    const MAIL_SMTP_USER = 'noreply@odisey.ru';
    const MAIL_SMTP_PASS = '';
    const MAIL_SMTP_HOST = '';
    const MAIL_SMTP_PORT = 465;
    const MAIL_SMTP_SECURE = 'ssl';
    const MAIL_SMTP_AUTH = true;
    const MAIL_NOTIFY = ['rayz@rayz.ru'];

    public static function hybridAuthConfig() {

        return [
            "callback" => Helpers::getCurrentURL(),
            "providers" => [
                "Google" => [
                    "enabled" => true,
                    "keys" => [
                        "id" => "",
                        "secret" => "",
                    ],
                    'scope' => 'profile https://www.googleapis.com/auth/plus.profile.emails.read',

                ],
                "Vkontakte" => [
                    "enabled" => true,
                    "scope" => "email",
                    "keys" => ["id" => "5661330", "secret" => ""], 
                ],
                "Facebook" => [
                    "enabled" => true,
                    'scope' => ['email'],
                    "keys" => ["id" => "667292253419303", "secret" => ""],
                ],
                "Twitter" => [
                    "enabled" => true,
                    "keys" => ["key" => "Gpd8TBCG2YSBlXILpN3FY5ZB3", "secret" => ""],
                    "includeEmail" => true,
                ],
                "Yandex" => [
                    "enabled" => true,
                    "keys" => ["id" => "d2673ff642a940cda7ec2d197a48e779", "secret" => ""], 
                ],
            ],
            "debug_mode" => false,
            "debug_file" => "",
        ];
    }
}
