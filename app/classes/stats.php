<?php

namespace odissey;

class Stats
{

    const SECTET_COOKIE_KEY = 'scodissey';

    /**
     * @var string $secret Secret Tracking Key
     */
    private $secret;

    public function __construct() {
        $this->initSecretCookie();
    }

    public function getSecret() {
        return $this->secret;
    }

    public function initSecretCookie() {
        $this->secret = !isset($_COOKIE[self::SECTET_COOKIE_KEY])
            ? Helpers::genUUID()
            : $_COOKIE[self::SECTET_COOKIE_KEY];
        if (!isset($_COOKIE[self::SECTET_COOKIE_KEY])) {
            setcookie(self::SECTET_COOKIE_KEY, $this->secret, time() + 60 * 60 * 24 * 3000, '/');
        }
    }
}
