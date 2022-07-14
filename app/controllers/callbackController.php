<?php

namespace odissey;

class CallbackController extends Controller
{

    public $statuses = [
        'new' => 'Новый',
        'accepted' => 'Принят',
        'dismissed' => 'Отменен',
    ];

    public $labels = [
        'new' => 'primary',
        'accepted' => 'success',
        'dismissed' => 'warning',
    ];

    public function __construct() {
        parent::__construct();
    }


    public function getCallbacksCount() {
        return $this->db
            ->where('status', ['new'], 'IN')
            ->getValue('callbacks', 'count(*)');
    }

    public function setStatus($id, $status) {
        return $this->db
            ->where('id', $id)
            ->update('callbacks', ['status' => $status]);
    }

    public function getCallbacks($options = []) {

        $fields = [
            'c.id',
            'c.name',
            'c.phone',
            'c.comment',
            'c.added',
            'c.status',
        ];

        $opts = [
            'page' => isset($options['page']) ? $options['page'] : 1,
            'pagerWidth' => isset($options['pagerWidth']) ? $options['pagerWidth'] : 10,
            'limit' => isset($options['limit']) ? $options['limit'] : 25,
            'query' => isset($options['query']) ? $options['query'] : null,
            'status' => isset($options['status']) ? $options['status'] : null,
            'id' => isset($options['id']) ? $options['id'] : null,
        ];

        $page = $opts['page'];
        $pagerWidth = $opts['pagerWidth'];

        if (!is_null($opts['id'])) {
            $this->db->where('c.id', $opts['id']);
        }

        if (!is_null($opts['status'])) {
            if (is_array($opts['status']) && count($opts['status'])) {
                $this->db->where('c.status', $opts['status'], 'IN');
            } else {
                $this->db->where('c.status', $opts['status']);
            }
        }

        $this->db
            ->orderBy('c.added', 'DESC')
            ->groupBy('c.id');

        if (!is_null($opts['id'])) {
            return $this->db->getOne('callbacks c', $fields);;
        } else {
            $this->db->pageLimit = $opts['limit'];
            $callbacks = $this->db
                ->arraybuilder()
                ->paginate("callbacks c", $page, $fields);
            $pagerStart =
                ($page - abs($pagerWidth / 2)) < 0
                    ? 0 : (($page + ceil($pagerWidth / 2)) > $this->db->totalPages
                    ? $this->db->totalPages - $pagerWidth
                    : $page - abs($pagerWidth / 2));

            return [
                'items' => $callbacks,
                'pages' => $this->db->totalPages,
                'page' => $opts['page'],
                'pagerStart' => $pagerStart,
                'pagerEnd' => $this->db->totalPages > $pagerWidth ? $pagerStart + $pagerWidth : $this->db->totalPages,
                'query' => $opts['query'],
                'count' => $this->db->totalCount,
            ];
        }
    }

    public function add($data) {
        if ($this->db->insert('callbacks', $data)) {
            $id = $this->db->getInsertId();
            return $id;
        }
        return false;
    }

    public function getCallbackStatusesCounts() {
        $statuses = $this->db
            ->groupBy('status')
            ->get('callbacks', null, ['status', 'COUNT(status) as cnt']);
        $res = [];
        foreach ($statuses as $status) {
            $res[$status['status']] = $status['cnt'];
        }

        return $res;
    }
}
