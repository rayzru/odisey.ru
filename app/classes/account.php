<?php

namespace odissey;

class Account
{
	public $id;
	public $email;
	public $phone;
	public $identifier;
	public $role = 'user';

	const ACCOUNT_ADMIN = 'admin';
	const ACCOUNT_USER = 'user';

	public function __construct($data = null) {
		if (!empty($data)) {
			$this->id = isset($data['id']) ? $data['id'] : null;
			$this->email = isset($data['email']) ? $data['email'] : null;
			$this->identifier = isset($data['identifier']) ? $data['identifier'] : null;
			$this->phone = isset($data['phone']) ? $data['phone'] : null;
			$this->role = isset($data['role']) ? $data['role'] : null;
		}
	}

	/**
	 * @return bool
	 */
	public function isAdmin() {
		return $this->role === self::ACCOUNT_ADMIN;
	}

	/**
	 * @return bool
	 */
	public function isLogged() {
		return is_numeric($this->id);
	}

	public function reset() {
		$this->id = null;
		$this->email = null;
		$this->phone = null;
		$this->identifier = null;
		$this->role = null;
	}

	public function getData() {
		return [
			'id'         => $this->id,
			'email'      => $this->email,
			'phone'      => $this->phone,
			'identifier' => $this->identifier,
			'role'       => $this->role
		];
	}
}
