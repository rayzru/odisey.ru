<?php

namespace odissey;

class Application
{

    public static $namespaces = [
        'admin',
        'about',
        'stats',
        'service',
        'catalog',
        'contacts',
        'assets',
        'feed',
        'promo',
        'search',
        'my',
        'tos',
        'info',
        'callbacks',
    ];

    public function __construct()
    {
    }

    public function getNamespaces()
    {
        return self::$namespaces;
    }
}
