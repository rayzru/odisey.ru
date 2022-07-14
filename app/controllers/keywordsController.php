<?php

namespace odissey;

class KeywordsController extends Controller
{

    public function __construct() {
        parent::__construct();
    }

    public function addSingle($keyword) {
        $this->db->insert('keywords', ['keyword' => $keyword]);

        return $this->db->getInsertId();
    }

    public function add($keyword) {
        $id = $this->getId($keyword);

        return ($id) ? $id : $this->addSingle($keyword);
    }

    public function query($query) {
        if (!empty(trim($query))) {
            $this->db->where('keyword', '%'.$query.'%', 'LIKE');
        }

        return $this->db
            ->get('keywords');
    }

    public function getId($keyword) {
        $keyword = mb_strtolower($keyword);

        return $this->db
            ->where('keyword', $keyword)
            ->getValue('keywords', 'id');
    }
}
