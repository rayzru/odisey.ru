<?php

namespace odissey;

class AccountController extends Controller
{

    /**
     * @var Account
     */
    private $account;

    // private $providers = ['google', 'twitter', 'mailru', 'yandex', 'facebook', 'vkontakte', 'odnoklassniki'];

    /**
     * AccountController constructor.
     */
    public function __construct() {
        parent::__construct();
        $sessionData = isset($_SESSION['account']) ? unserialize($_SESSION['account']) : null;
        $this->account = new Account($sessionData);
        if (property_exists($this->account, 'email')) {
            $userData = $this->getAccountByEmail($this->account->email);
            $this->account = new Account($userData);
        }
    }

    /**
     * @return bool
     */
    public function isLogged() {
        return $this->account->isLogged();
    }

    /**
     * @return bool
     */
    public function isAdmin() {
        return $this->account->role === Account::ACCOUNT_ADMIN;
    }

    /**
     *  Removes all current user data
     */
    public function logout() {
        if ($this->isLogged()) {
            unset($_SESSION['account']);
        }
    }

    /**
     * @return Account
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * @param      $identifier
     * @param null $password
     *
     * @return bool
     */
    public function updateProfile($identifier, $password = null) {
        $this->db
            ->where('id', $this->account->id)
            ->update('users', ['identifier' => $identifier]);
        $data = $this->getAccountByEmail($this->account->email);
        $this->setAccount($data);
        if (!empty($password)) {
            return $this->db
                ->where('id', $this->account->id)
                ->update('users', ['password' => md5($password)]);
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return array|bool
     */
    public function getAccountById($id) {
        if (!is_numeric($id)) {
            return false;
        }

        return $this->db
            ->where('id', $id)
            ->getOne('users');
    }

    /**
     * @param $email
     *
     * @return array|bool
     */
    public function getAccountByEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $this->db
            ->rawQueryOne("SELECT * FROM users WHERE email = '{$email}' LIMIT 1");
    }

    public function authEmail($email, $password, $role = null) {
        if (empty($password) || empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        if (isset($role) && in_array($role, [Account::ACCOUNT_USER, Account::ACCOUNT_ADMIN])) {
            $this->db->where('role', $role);
        }

        return $this->db
            ->where('flag_active', 1)
            ->where('email', $email)
            ->where('password', md5($password))
            ->has('users');
    }

    /**
     * @param $data
     */
    public function setAccount($data) {
        $this->account = new Account($data);
        $session_data = $this->account->getData();
        $_SESSION['account'] = serialize($session_data);
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function isEmailUsed($email) {
        return $this->db
            ->where('email', $email)
            ->has('users');
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function isEmailActive($email) {
        return $this->db
            ->where('email', $email)
            ->where('flag_active', 1)
            ->has('users');
    }

    /**
     * Register User
     *
     * @param      $email
     * @param null $password
     * @param null $identifier
     *
     * @return bool|int
     */
    public function register($email, $password = null, $identifier = null) {
        if ($this->db->insert(
            'users',
            [
                'email' => $email,
                'password' => md5($password),
                'identifier' => $identifier,
            ]
        )) {
            return $this->db->getInsertId();
        };

        return false;
    }
}
