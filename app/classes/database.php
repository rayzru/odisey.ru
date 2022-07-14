<?php

namespace odissey;

use MysqliDb;

class Database extends MysqliDb
{

    /**
     * Static instance of self
     *
     * @var MysqliDb
     */
    protected static $instance;

    public function __construct() {

        parent::__construct(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASS,
            Configuration::DATABASE_DB,
            3306,
            'UTF8'
        );

        try {
            $this->connect('default');
        } catch (\Exception $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    }

    final public static function getInstance():MysqliDb {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
