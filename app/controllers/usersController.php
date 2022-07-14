<?php

namespace odissey;

class UsersController extends Controller
{

    private $fields = [
        'u.id',
        'u.identifier',
        'u.email',
        'u.password',
        'u.role',
        'u.flag_active',
        'uk.ukey',
        'uk.added as key_added',
    ];

    public function __construct() {
        parent::__construct();
    }

    public function get($params = []) {
        $opts = [
            'page' => isset($params['page']) ? $params['page'] : 1,
            'pagerWidth' => isset($params['pagerWidth']) ? $params['pagerWidth'] : 10,
            'limit' => isset($params['limit']) ? $params['limit'] : 25,
            'id' => isset($params['id']) ? $params['id'] : null,
            'query' => isset($params['query']) ? $params['query'] : null,
        ];

        $page = $opts['page'];
        $pagerWidth = $opts['pagerWidth'];

        $this->db
            ->join('users_confirmations uk', 'u.id = uk.user_id', 'LEFT');

        if ($opts['id']) {
            return $this->db
                ->where('u.id', $opts['id'])
                ->getOne('users u', $this->fields);
        }

        if ($opts['query']) {
            $this->db
                ->where('u.identifier', '%'.$opts['query'].'%', 'LIKE')
                ->orWhere('u.email', '%'.$opts['query'].'%', 'LIKE');
        }

        $this->db->pageLimit = $opts['limit'];

        $users = $this->db
            ->arraybuilder()
            ->paginate("users u", $opts['page'], $this->fields);

        $pagerStart =
            ($page - abs($pagerWidth / 2)) < 0
                ? 0 : (($page + ceil($pagerWidth / 2)) > $this->db->totalPages
                ? $this->db->totalPages - $pagerWidth
                : $page - abs($pagerWidth / 2));

        return [
            'items' => $users,
            'pages' => $this->db->totalPages,
            'page' => $opts['page'],
            'pagerStart' => $pagerStart,
            'pagerEnd' => $this->db->totalPages > $pagerWidth ? $pagerStart + $pagerWidth : $this->db->totalPages,
            'query' => $opts['query'],
            'count' => $this->db->totalCount,
        ];
    }

    /**
     * @param $user_id
     *
     * @return string
     */
    public function addConfirmationKey($user_id) {
        // $key = hash('sha256', microtime());
        // $key = sha1(microtime());
        $key = Helpers::genUUID();
        $this->db
            ->where('user_id', $user_id)
            ->delete('users_confirmations');

        $this->db
            ->insert('users_confirmations', ['user_id' => $user_id, 'ukey' => $key]);

        return $key;
    }

    /**
     * @param $user_id
     *
     * @return array
     */
    public function getConfirmationKey($user_id) {
        return $this->db
            ->where('user_id', $user_id)
            ->getOne('users_confirmations');
    }

    /**
     * @param $key
     *
     * @return bool|int
     */
    public function revealConfirmationKey($key) {
        $user_confirmations = $this->db
            ->where('ukey', $key)
            ->getOne('users_confirmations');

        if (isset($user_confirmations['user_id'])) {
            $this->db
                ->where('ukey', $key)
                ->delete('users_confirmations');

            return $user_confirmations['user_id'];
        }

        return false;
    }

    public function activate($user_id) {
        $this->setActivity($user_id, 1);
    }

    public function deactivate($user_id) {
        $this->setActivity($user_id, 0);
    }

    public function setActivity($user_id, $activity = 1) {
        $this->db
            ->where('id', $user_id)
            ->update('users', ['flag_active' => $activity]);
    }

    public function setRole($user_id, $role = 'user') {
        $this->db
            ->where('id', $user_id)
            ->update('users', ['role' => $role]);
    }

    public function updatePassword($user_id, $password) {
        return $this->db
            ->where('id', $user_id)
            ->update('users', ['password' => md5($password)]);
    }

}
