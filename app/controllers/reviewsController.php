<?php

namespace odissey;

class ReviewsController extends Controller
{
	private $account;
	private $accountController;

	private $fields = [
		'r.id',
		'r.rating',
		'r.added',
		'r.updated',
		'r.review',
		'r.user_id',
		'r.item_id',
		'r.anonymously',
		'r.status'
	];

	public $statuses = [
		'moderated'    => 'На модерации',
		'published'   => 'Опубликован',
		'rejected' => 'Отменен',
		'deleted'  => 'Удален'
	];

	public $labels = [
		'moderated'    => 'warning',
		'published'   => 'success',
		'rejected' => 'dark',
		'deleted'  => 'error'
	];

	public function __construct() {
		parent::__construct();
		$this->accountController = new AccountController();
		$this->account = $this->accountController->getAccount();
	}

	public function addItemReview($data) {
		if (isset($data['review_id'])) {
			unset($data['review_id']);
		}
		return $this->db->insert('catalog_items_reviews', $data);
	}

	public function updateItemReview($data) {
		if ($data['review_id']) {
			$id = $data['review_id'];
			unset($data['review_id']);
			return $this->db
				->where('id', $id)
				->update('catalog_items_reviews', $data, 1);
		} else {
			return false;
		}

	}

	public function getUserItemReviews($id) {
		if ($this->account->isLogged()) {
			return $this->db
				->where('r.item_id', $id)
				->getOne('catalog_items_reviews r', $this->fields);
		} else {
			return false;
		}
	}

	public function getReviews($options = []) {

		$opts = [
			'page'       => isset($options['page']) ? $options['page'] : 1,
			'pagerWidth' => isset($options['pagerWidth']) ? $options['pagerWidth'] : 10,
			'limit'      => isset($options['limit']) ? $options['limit'] : 25,
			'query'      => isset($options['query']) ? $options['query'] : null,
			'status'     => isset($options['status']) ? $options['status'] : null,
			'id'         => isset($options['id']) ? $options['id'] : null,
			'item_id'    => isset($options['item_id']) ? $options['item_id'] : null,
			'user'       => isset($options['user']) ? $options['user'] : null
		];

		$page = $opts['page'];
		$pagerWidth = $opts['pagerWidth'];

		if (!is_null($opts['id'])) {
			$this->db->where('r.id', $opts['id']);
		}

		if (!is_null($opts['item_id'])) {
			$this->db->where('r.item_id', $opts['item_id']);
		}

		if (!is_null($opts['status'])) {
			if (is_array($opts['status']) && count($opts['status'])) {
				$this->db->where('r.status', $opts['status'], 'IN');
			} else {
				$this->db->where('r.status', $opts['status']);
			}
		}

		if (!is_null($opts['user'])) {
			$this->db->where('r.user_id', $opts['user']);
		}

		$this->db
			->join('users u', 'u.id = r.user_id', 'LEFT')
			->join('catalog_items i', 'i.id = r.item_id', 'LEFT')
			->orderBy('r.updated', 'DESC')
			->groupBy('r.id');

		$fields = $this->fields;
		$fields = array_merge($fields, ['u.email', 'u.identifier']);
		$fields = array_merge($fields, ['i.title as item_title', 'i.articul as item_articul']);

		if (!is_null($opts['id'])) {
			$order = $this->db->getOne('catalog_items_reviews r', $fields);
			return $order;
		} else {
			$this->db->pageLimit = $opts['limit'];
			$orders = $this->db
				->arraybuilder()
				->paginate("catalog_items_reviews r", $page, $fields);
			$pagerStart =
				($page - abs($pagerWidth / 2)) < 0
					? 0 : (($page + ceil($pagerWidth / 2)) > $this->db->totalPages
					? $this->db->totalPages - $pagerWidth
					: $page - abs($pagerWidth / 2));

			return [
				'items'      => $orders,
				'pages'      => $this->db->totalPages,
				'page'       => $opts['page'],
				'pagerStart' => $pagerStart,
				'pagerEnd'   => $this->db->totalPages > $pagerWidth ? $pagerStart + $pagerWidth : $this->db->totalPages,
				'query'      => $opts['query'],
				'count'      => $this->db->totalCount
			];
		}
	}

	public function getReviewStatusesCounts() {
		$statuses = $this->db
			->groupBy('status')
			->get('catalog_items_reviews', null, ['status', 'COUNT(status) as cnt']);

		$res = [];

		foreach ($statuses as $status) {
			$res[$status['status']] = $status['cnt'];
		}
		return $res;
	}

	public function setStatus($id, $status) {
		return $this->db
			->where('id', $id)
			->update('catalog_items_reviews', ['status' => $status]);
	}

	public function getReviewsCount() {
		return $this->db
			->where('status', ['moderated'], 'IN')
			->getValue('catalog_items_reviews', 'count(*)');
	}

	public function updateReview($id, $review) {
		return (is_numeric($id) && !empty($review)) &&
			$this->db
				->where('id', $id)
				->update('catalog_items_reviews', ['review' => $review]);
	}

}
