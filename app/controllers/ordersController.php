<?php

namespace odissey;

class OrdersController extends Controller
{
	private $account;
	private $accountController;
	private $guestCart = [];
	public $guestCartKey = 'cartCookie';
	public $statuses = [
		'added' => 'Новый',
		'queued' => 'Принят',
		'rejected' => 'Отменен',
		'closed' => 'Закрыт',
		'deleted' => 'Удален'
	];

	public $labels = [
		'added' => 'primary',
		'queued' => 'success',
		'rejected' => 'dark',
		'closed' => 'light',
		'deleted' => 'warning'
	];

	public $cartWarnings = [
		'priceempty' => '<h4>Товары без стоимости</h4>В корзине находятся товары без указания стоимости. ' .
			'Cтоимости данных товаров уточняется и может быть получена у менеджеров компании',
		'stockorder' => '<h4>Товары под заказ</h4>Сроки поставки товаров, доступные для заказа следует уточнять ' .
			'у менеджеров после оформления товаров.',
		'stocknone' => '<h4>Отсутствующие товары</h4>В корзине есть товары, отсутствующие на складах компании и без ' .
			'гарантий поставок от производителя.' .
			'Компания не берет на себя отвественность за поставку подобных товаров.',
	];

	public function __construct()
	{
		parent::__construct();
		$this->accountController = new AccountController();
		$this->account = $this->accountController->getAccount();
		$this->getGuestCart();

		if ($this->account->isLogged() && count($this->guestCart)) {
			// fill cart with guest cart items
			$this->db
				->where('user_id', $this->account->id)
				->delete('cart');
			foreach ($this->guestCart as $item_id => $quantity) {
				$this->addCartItem($item_id, $quantity);
			}
			$this->guestCart = [];
			$this->setGuestCartCookie();
		}
	}

	public function clearCart()
	{
		if ($this->account->isLogged()) {
			return $this->db
				->where('user_id', $this->account->id)
				->delete('cart');
		} else {
			$this->guestCart = [];
			$this->setGuestCartCookie();
		}
		return false;
	}

	public function getCart()
	{
		if ($this->account->isLogged()) {
			return $this->db
				->join('catalog_items i', 'c.item_id = i.id', 'LEFT')
				->join(
					'catalog_items_prices pr',
					'pr.item_id = i.id and pr.date = ' .
					'(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 WHERE pr2.item_id = i.id ' .
					'GROUP BY pr2.item_id LIMIT 1)',
					'LEFT'
				)
				->where('c.user_id', $this->account->id)
				->get('cart c', null, [
					'c.id as cart_id',
					'c.quantity as quantity',
					'pr.price',
					'pr.date as price_date',
					'i.*'
				]);
		} else {
			if (count($this->guestCart)) {
				$keys = array_keys($this->guestCart);
				$items = $this->db
					->join(
						'catalog_items_prices pr',
						'pr.item_id = i.id and pr.date = ' .
						'(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 WHERE pr2.item_id = i.id ' .
						'GROUP BY pr2.item_id LIMIT 1)',
						'LEFT'
					)
					->where('i.id', $keys, 'IN')
					->groupBy('i.id')
					->get('catalog_items i', null, [
						'i.id as cart_id',
						'pr.price',
						'pr.date as price_date',
						'i.*'
					]);
				$i = array_map(function ($item) {
					$item['quantity'] = $this->guestCart[$item['id']];
					return $item;
				}, $items);
				return $i;
			}
		}
		return [];
	}

	public function getCartCount()
	{
		if ($this->account->isLogged()) {
			return $this->db
				->where('user_id', $this->account->id)
				->getValue('cart', 'count(*)');
		} else {
			return count($this->guestCart);
		}
	}

	public function getOrdersCount()
	{
		return $this->db
			->where('status', ['added', 'queued'], 'IN')
			->getValue('orders', 'count(*)');
	}

	public function getCartItemCount($item_id)
	{
		$items = $this->db
			->where('user_id', $this->account->id)
			->where('item_id', $item_id)
			->getValue('cart', 'quantity');
		return (isset($items) && $items !== null) ? $items : 0;
	}

	public function getCartStatuses()
	{
		$empty_prices = $this->db
			->where('user_id', $this->account->id)
			->where('pr.price', 0)
			->join('catalog_items_prices as pr', 'pr.item_id = c.item_id', 'LEFT')
			->getValue('cart c', 'count(*)');
		$stock_order = $this->db
			->where('user_id', $this->account->id)
			->where('i.stock', 'order')
			->join('catalog_items as i', 'i.id = c.item_id', 'LEFT')
			->getValue('cart c', 'count(*)');
		$stock_none = $this->db
			->where('user_id', $this->account->id)
			->where('i.stock', 'none')
			->join('catalog_items as i', 'i.id = c.item_id', 'LEFT')
			->getValue('cart c', 'count(*)');

		return [
			'priceempty' => $empty_prices,
			'stockorder' => $stock_order,
			'stocknone' => $stock_none
		];
	}

	public function deleteCartItem($id)
	{
		if ($this->account->isLogged()) {
			return $this->db
				->where('user_id', $this->account->id)
				->where('item_id', $id)
				->delete('cart');
		} else {
			if (isset($this->guestCart[$id])) {
				unset($this->guestCart[$id]);
				$this->setGuestCartCookie();
				return true;
			} else {
				return false;
			}
		}
	}

	public function setGuestCartCookie()
	{
		$expire = time() + 60 * 60 * 24 * 300; // 300 days
		setcookie($this->guestCartKey, base64_encode(serialize($this->guestCart)), $expire, '/');
	}

	public function updateCartItem($id, $quantity)
	{
		if ($this->account->isLogged()) {
			$status = $this->db
				->where('user_id', $this->account->id)
				->where('item_id', $id)
				->update('cart', ['quantity' => $quantity]);
		} else {
			$this->guestCart[$id] = $quantity;
			$this->setGuestCartCookie();
			$status = true;
		}
		return $status;
	}

	public function addCartItem($item_id, $quantity = 1)
	{
		$q = $this->getCartItemCount($item_id);
		if ($q) {
			return $this->db
				->where('user_id', $this->account->id)
				->where('item_id', $item_id)
				->update('cart', ['quantity' => $q + $quantity,]);
		} else {
			return $this->db->insert('cart', [
				'user_id' => $this->account->id,
				'item_id' => $item_id,
				'quantity' => $quantity
			]);
		}
	}

	public function getGuestCart()
	{
		$key = $this->guestCartKey;
		$this->guestCart = isset($_COOKIE[$key]) && Helpers::isBase64($_COOKIE[$key])
			? unserialize(base64_decode($_COOKIE[$key]))
			: [];
		return $this->guestCart;
	}

	public function addGuestCartItem($item_id, $quantity = 1)
	{
		$this->guestCart[$item_id] = isset($this->guestCart[$item_id])
			? $this->guestCart[$item_id] + $quantity
			: $quantity;
		$this->setGuestCartCookie();
	}

	public function setStatus($id, $status)
	{
		return $this->db
			->where('id', $id)
			->update('orders', ['status' => $status]);
	}

	public function getOrderItems($id, $user_id = null)
	{
		$this->db
			->join('orders_items o', 'i.id = o.item_id', 'RIGHT OUTER')
			->join('orders', 'o.order_id = orders.id')
			->where('orders.id', $id);

		if (!empty($user_id)) {
			$this->db->where('orders.user_id', $user_id);
		}
		return $this->db->get('catalog_items i', null, [
			'i.title', 'i.id as item_id', 'i.articul', 'i.unit', 'o.stock', 'o.price', 'o.quantity'
		]);
	}

	public function getOrderStatusesCounts()
	{
		$statuses = $this->db
			->groupBy('status')
			->get('orders', null, ['status', 'COUNT(status) as cnt']);
		$res = [];
		foreach ($statuses as $status) {
			$res[$status['status']] = $status['cnt'];
		}
		return $res;
	}

	public function getOrders($options = [])
	{

		$fields = [
			'o.id',
			'o.user_id',
			'o.added',
			'o.status'
		];

		$opts = [
			'page' => isset($options['page']) ? $options['page'] : 1,
			'pagerWidth' => isset($options['pagerWidth']) ? $options['pagerWidth'] : 10,
			'limit' => isset($options['limit']) ? $options['limit'] : 25,
			'query' => isset($options['query']) ? $options['query'] : null,
			'status' => isset($options['status']) ? $options['status'] : null,
			'id' => isset($options['id']) ? $options['id'] : null,
			'user' => isset($options['user']) ? $options['user'] : null
		];

		$page = $opts['page'];
		$pagerWidth = $opts['pagerWidth'];

		if (!is_null($opts['id'])) {
			$this->db->where('o.id', $opts['id']);
		}

		if (!is_null($opts['status'])) {
			if (is_array($opts['status']) && count($opts['status'])) {
				$this->db->where('o.status', $opts['status'], 'IN');
			} else {
				$this->db->where('o.status', $opts['status']);
			}
		}

		if (!is_null($opts['user'])) {
			$this->db->where('o.user_id', $opts['user']);
		}

		$this->db
			->join('users u', 'u.id = o.user_id', 'LEFT')
			->join('orders_items i', 'i.order_id = o.id', 'LEFT')
			->orderBy('o.added', 'DESC')
			->groupBy('o.id');

		$fields = array_merge($fields, ['COUNT(i.id) as items_count']);
		$fields = array_merge($fields, ['u.email', 'u.identifier']);

		if (!is_null($opts['id'])) {
			$order = $this->db->getOne('orders o', $fields);
			$order['items'] = $this->getOrderItems($opts['id']);
			return $order;
		} else {
			$this->db->pageLimit = $opts['limit'];
			$orders = $this->db
				->arraybuilder()
				->paginate("orders o", $page, $fields);
			$pagerStart =
				($page - abs($pagerWidth / 2)) < 0
					? 0 : (($page + ceil($pagerWidth / 2)) > $this->db->totalPages
					? $this->db->totalPages - $pagerWidth
					: $page - abs($pagerWidth / 2));

			return [
				'items' => $orders,
				'pages' => $this->db->totalPages,
				'page' => $opts['page'],
				'pagerStart' => $pagerStart,
				'pagerEnd' => $this->db->totalPages > $pagerWidth ? $pagerStart + $pagerWidth : $this->db->totalPages,
				'query' => $opts['query'],
				'count' => $this->db->totalCount
			];
		}
	}

	public function orderCreate()
	{
		if ($this->db->insert('orders', ['user_id' => $this->account->id])) {
			$order_id = $this->db->getInsertId();
			$this->db->rawQuery(
				'INSERT INTO orders_items (order_id, item_id, quantity, stock, price) ' .
				'SELECT ?, i.id as item_id, quantity, i.stock, pr.price AS price FROM cart ' .
				'LEFT JOIN catalog_items_prices pr ON pr.item_id = cart.item_id and pr.date = ' .
				'(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 ' .
				'WHERE pr2.item_id = cart.item_id GROUP BY pr2.item_id LIMIT 1) ' .
				'LEFT JOIN catalog_items i ON (i.id = cart.item_id)' .
				'WHERE cart.user_id = ? GROUP BY cart.item_id',
				[$order_id, $this->account->id]
			);
			$this->clearCart();
			return $order_id;
		}
		return false;
	}
}
