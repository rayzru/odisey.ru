<?php

namespace odissey;

class Controller
{
    protected $db;
    protected $tree;

    public function __construct() {
        $this->db = Database::getInstance();
//        $this->tree = new \Baobab($this->db->mysqli(), 'tree');
    }
}
