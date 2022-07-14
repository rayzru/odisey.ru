<?php

namespace odissey;

class CatalogController extends Controller
{

    private $account;

    private $accountController;

    private $order = [
        'order' => 'i.order_id',
        'price' => 'pr.price',
        'title' => 'i.title',
        'stock' => 'i.stock',
        'rating' => 'rating',
        'votes' => 'votes',
    ];

    public $qccKeys = [
        'qcc_description' => 'Описание',
        'qcc_seo_description' => 'SEO Описание',
        'qcc_keywords' => 'Ключевые слова',
        'qcc_articul' => 'Артикул',
        'qcc_images' => 'Изображения',
        'qcc_features' => 'Характеристики',
        'qcc_price' => 'Цены',
    ];

    public $qccCategoryKeys = [
        'qcc_description' => 'Описание',
        'qcc_seo_description' => 'SEO Описание',
        'qcc_keywords' => 'Ключевые слова',
        'qcc_image' => 'Изображениe',
        'qcc_features' => 'Характеристики',
    ];

    public $qccValues = [
        'none' => [
            'l' => 'Пусто',
            'c' => 'danger',
        ],
        'error' => [
            'l' => 'Ошибка',
            'c' => 'danger',
        ],
        'weak' => [
            'l' => 'Мало',
            'c' => 'warning',
        ],
        'overflow' => [
            'l' => 'Много',
            'c' => 'warning',
        ],
        'normal' => [
            'l' => 'Норма',
            'c' => 'success',
        ],
    ];


    private $featureTypes = [
        '' => '(не указан)',
        'range' => 'Диапазон значений',
        'single' => 'Одно из значений',
        'multiple' => 'Несколько значений',
    ];

    private $popularCategoriesCookieKey = 'popularCat';

    private $popularCategories = [];

    private $defaultpopularCategories = [1 => 1, 255 => 1, 580 => 1, 1338 => 1];

    private $catalog_items_fields = [
        'i.id',
        'i.title',
        'i.seo_title',
        'i.articul',
        'i.description',
        'i.seo_description',
        'i.unit',
        'i.stock',
        'i.flag_active',
        'i.flag_new',
        'i.flag_special',
        'i.flag_top',
        'i.flag_commission',
        'i.flag_price_warn',
    ];

    private $catalog_items_fields_brief = [
        'i.id',
        'i.title',
        'i.articul',
        'i.flag_active',
        'i.flag_new',
        'i.flag_special',
        'i.flag_top',
        'i.flag_commission',
        'i.flag_price_warn',
    ];

    private $catalog_ns_fields = [
        'ns.order_right',
        'ns.order_left',
        'ns.order_id',
        'ns.order_level',
        'IF((c.order_right - c.order_left) = 1, 1, 0) as is_leaf',
    ];

    private $catalog_categories_fields = [
        'c.id',
        'c.pid',
        'c.title',
        'c.seo_title',
        'c.tag',
        'c.description',
        'c.seo_description',
        'c.flag_active',
        'c.appearance',
    ];

    private $catalog_categories_fields_brief = [
        'c.id',
        'c.pid',
        'c.title',
    ];

    private $stocks = [
        'stock' => [
            'title' => 'В наличии',
            'description' => 'Товар имеется в наличии на наших складах.',
        ],
        'order' => [
            'title' => 'Под заказ',
            'description' => 'На данный момент данная позиция отсутствует на наших складах. '.
                'Товар доступен для приобретения под заказ. Для более точных данных обратитесь к нашим менеджерам.',
        ],
        'none' => [
            'title' => 'Временно отсутствует',
            'description' => 'Товар временно отсутствует у производителя',
        ],
    ];

    public function __construct() {
        parent::__construct();
        $this->accountController = new AccountController();
        $this->account = $this->accountController->getAccount();
        $this->getPopularCategoriesCookies();
    }

    public function getOrderKeys() {
        return array_keys($this->order);
    }

    public function totalItems($activeOnly = true) {
        if ($activeOnly) {
            $this->db->where('flag_active', 1);
        }

        return $this->db->getValue('catalog_items', 'count(id)');
    }

    public function setPopularCategoriesCookies() {
        $expire = time() + 60 * 60 * 24 * 300; // 300 days
        setcookie($this->popularCategoriesCookieKey, base64_encode(serialize($this->popularCategories)), $expire, '/');
    }


    public function getPopularCategoriesCookies() {
        $this->popularCategories =
            isset($_COOKIE[$this->popularCategoriesCookieKey]) &&
            Helpers::isBase64($_COOKIE[$this->popularCategoriesCookieKey]) ?
                unserialize(base64_decode($_COOKIE[$this->popularCategoriesCookieKey])) :
                $this->defaultpopularCategories;

        return $this->popularCategories;
    }

    public function increaseCategoryPopularity($categoryId) {
        if (isset($this->popularCategories[$categoryId])) {
            $this->popularCategories[$categoryId]++;
        } else {
            $this->popularCategories[$categoryId] = 1;
        }
        arsort($this->popularCategories);
        $this->setPopularCategoriesCookies();
    }

    public function getPopularCategories($length = 4) {

        if (count($this->popularCategories) == 0) {
            $this->popularCategories = $this->defaultpopularCategories;
        }

        if (count($this->popularCategories) < 4) {
            $this->popularCategories = array_merge($this->popularCategories, $this->defaultpopularCategories);
        }

        $categoriesIds = array_keys($this->popularCategories);
        $selectedCategories = array_splice($categoriesIds, 0, $length);
        $categories = $this->getCategories(['id' => $selectedCategories]);
        $result = [];

        foreach ($selectedCategories as $k => $tc) {
            foreach ($categories as $cat) {
                if ($cat['id'] === $tc) {
                    $result[$k] = $cat;
                }
            }
        }

        return $result;
    }

    public function getCategories($options = []) {
        $opts = [
            'active' => isset($options['active']) ? $options['active'] : null,
            'parent' => isset($options['parent']) ? $options['parent'] : null,
            'query' => isset($options['query']) ? $options['query'] : null,
            'id' => isset($options['id']) ? $options['id'] : null,
            'brief' => isset($options['brief']) ? $options['brief'] : false,
            'image' => isset($options['image']) ? (boolean)$options['image'] : true,
        ];

        $fields = $opts['brief'] ? $this->catalog_categories_fields_brief : $this->catalog_categories_fields;

        if ($opts === []) {
            $opts['parent'] = 0;
        }

        if (isset($opts['parent'])) {
            if (is_array($opts['parent'])) {
                $this->db
                    ->where('c.pid', $opts['parent'], 'IN');
            } elseif (is_numeric($opts['parent'])) {
                $this->db
                    ->where('c.pid', $opts['parent']);
            }
        }

        if (isset($opts['image']) && $opts['image']) {
            $fields = array_merge($fields, ['img.filename']);
            try {
                $this->db
                    ->join('catalog_category_images img', 'img.category_id = c.id', 'LEFT OUTER');
            } catch (\Exception $exception) {
                return false;
            }
        }

        if (isset($opts['active'])) {
            $this->db->where('c.flag_active', $opts['active'] ? 1 : 0);
        }

        if (isset($opts['query'])) {
            $q = $opts['query'];
            $this->db->where('c.title', "%{$q}%", 'LIKE');
        }

        if (isset($opts['id'])) {
            if (is_array($opts['id'])) {
                $this->db->where('c.id', $opts['id'], 'IN');
            } elseif (is_numeric($opts['id'])) {
                $this->db->where('c.id', $opts['id']);
            }
        }
        try {
            $this->db
                ->join('catalog_categories_items_count cntr', 'cntr.category_id = c.id', 'LEFT OUTER');
        } catch (\Exception $exception) {
            return false;
        }
        $fields = array_merge($fields, ['cntr.items as items_count']);

        try {
            $this->db->orderBy('c.order_id', 'ASC');
        } catch (\Exception $exception) {
            return false;
        }
        $categories = $this->db
            ->groupBy('c.id')
            ->get('catalog_categories c', null, $fields);

        return $categories;
    }

    public function calculateItemsCounts() {
        $this->db->rawQuery(
            "
	    REPLACE INTO catalog_categories_items_count(category_id, items)
          SELECT parent.id as category_id, COUNT(product.item_id) as items FROM catalog_categories parent
          LEFT OUTER JOIN catalog_categories node ON node.order_left BETWEEN parent.order_left AND parent.order_right
          LEFT OUTER JOIN catalog_categories_items product ON node.id = product.category_id
          LEFT OUTER JOIN catalog_items item ON product.item_id = item.id 
          WHERE item.flag_active = 1 and node.flag_active = 1 GROUP BY parent.id
        "
        );
    }

    public function getPath($id) {
        if (!is_numeric($id)) {
            return;
        }

        $fields = ['title', 'id', 'pid', 'order_level'];
        $category = $this->db
            ->where('id', $id)
            ->getOne('catalog_categories', $fields);

        $path = [];
        if ($this->db->count > 0) {
            $path[] = $category;
            $path = array_merge($this->getPath($category['pid']), $path);
        }

        return $path;
    }

    public function getQccItems($categoryId) {
        $fields = [
            'i.id',
            'i.title',
            'i.articul',
            'c.category_id',
            'q.description as qcc_description',
            'q.images as qcc_images',
            'q.seo_description as qcc_seo_description',
            'q.seo_keywords as qcc_keywords',
            'q.features as qcc_features',
            'q.price as qcc_price',
            'q.articul as qcc_articul',
        ];
        $f = implode(',', $fields);

        return $this->db->rawQuery(
            "
			SELECT {$f}
			FROM catalog_items i 
			LEFT JOIN catalog_categories_items c ON c.item_id = i.id 
			LEFT OUTER JOIN qcc_items q ON q.item_id = i.id
			WHERE  c.category_id = ?
			ORDER BY i.order_id",
            [$categoryId]
        );
    }

    public function getQccItemsTree() {

        $fields = [
            'i.id',
            'i.title',
            'i.articul',
            'c.category_id',
            'i.stock',
            'i.flag_active',
            'p.price as price',
            'q.description as qcc_description',
            'q.images as qcc_images',
            'q.seo_description as qcc_seo_description',
            'q.seo_keywords as qcc_keywords',
            'q.features as qcc_features',
            'q.price as qcc_price',
            'q.articul as qcc_articul',
        ];

        $f = implode(',', $fields);
        $items = $this->db->rawQuery(
            "
			SELECT {$f}
			FROM catalog_items i 
			LEFT JOIN catalog_categories_items c ON c.item_id = i.id 
			LEFT OUTER JOIN qcc_items q ON q.item_id = i.id 
			LEFT JOIN catalog_items_prices p ON p.item_id = i.id AND p.date = 
			(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 WHERE pr2.item_id = i.id GROUP BY pr2.item_id LIMIT 1)
			GROUP BY i.id ORDER BY i.order_id"

        );

        $newItems = [];
        foreach ($items as $item) {
            $newItems[$item['category_id']]['items'][] = $item;
        }

        foreach ($newItems as $k => $category) {
            if (!isset($newItems[$k]['qcc'])) {
                foreach ($this->qccKeys as $qccKey => $v) {
                    $newItems[$k]['qcc'][$qccKey] = [
                        'total' => 0,
                        'none' => 0,
                        'error' => 0,
                        'weak' => 0,
                        'overflow' => 0,
                        'normal' => 0,
                    ];
                }
            }
            foreach ($category['items'] as $newItem) {
                foreach ($this->qccKeys as $qccKey => $v) {
                    if (!isset($newItems[$k]['qcc'][$qccKey][$newItem[$qccKey]])) {
                        $newItems[$k]['qcc'][$qccKey][$newItem[$qccKey]] = 0;
                    }
                    $newItems[$k]['qcc'][$qccKey][$newItem[$qccKey]]++;
                }
            }
        }

        $s = $this->db->rawQueryOne("SELECT min(order_left) AS lft, max(order_right) AS rgt FROM catalog_ns");

        try {
            $this->db
                ->orderBy('order_left', 'asc')
                ->orderBy('order_right', 'asc');

        } catch (\Exception $exception) {
            return false;
        }

        $categories = $this->db
            ->where('order_left BETWEEN ? AND ?', [$s['lft'], $s['rgt']])
            ->get(
                'catalog_ns',
                null,
                [
                    'category_id',
                    'IF((order_right - order_left) = 1, 1, 0) as is_leaf',
                    'order_level',
                    'order_left',
                    'order_right',
                ]
            );

        $res = [];

        if ($categories) {
            foreach ($categories as $category) {
                $qccArray = [];
                foreach ($this->qccKeys as $qccKey => $v) {
                    if (isset($newItems[$category['id']])) {
                        $newItems[$category['id']]['qcc'][$qccKey]['total'] =
                            array_sum($newItems[$category['id']]['qcc'][$qccKey]);
                    }
                    $qccArray[$qccKey] =
                        $newItems[$category['id']]['qcc'][$qccKey]
                        ?? [
                            'total' => 0,
                            'none' => 0,
                            'error' => 0,
                            'weak' => 0,
                            'overflow' => 0,
                            'normal' => 0,
                        ];
                }
                $res[] = [
                    'id' => $category['id'],
                    'pid' => $category['pid'],
                    'lvl' => $category['order_level'],
                    'is_leaf' => $category['is_leaf'] == 1,
                    'title' => $category['title'],
                    'items' => $newItems[$category['id']]['items'] ?? [],
                    'qcc' => $qccArray,
                ];
            }
        }

        foreach ($res as $row) {
            if ($row['is_leaf'] === true && count($row['items']) > 0) {
                $this->qccApplyToUpperNodes($res, $row['id']);
            }
        }

        foreach ($res as $k => $row) {
            foreach ($this->qccKeys as $qccKey => $qkv) {
                foreach ($this->qccValues as $qccValue => $v) {
                    $res[$k]['qcc']['perc'][$qccKey][$qccValue] = ($res[$k]['qcc'][$qccKey]['total'])
                        ? round(100 / $res[$k]['qcc'][$qccKey]['total'] * $res[$k]['qcc'][$qccKey][$qccValue])
                        : 0;
                }
            }
        }

        return $res;
    }

    public function getQccCategoriesTree() {

        $fields = [
            'c.id',
            'c.pid',
            'c.title',
            'IF((order_right - order_left) = 1, 1, 0) as is_leaf',
            'q.description as qcc_description',
            'q.image as qcc_image',
            'q.seo_description as qcc_seo_description',
            'q.seo_keywords as qcc_keywords',
            'q.features as qcc_features',
        ];

        $f = implode(',', $fields);
        $categories = $this->db->rawQuery(
            "
			SELECT {$f}
			FROM catalog_categories c 
			LEFT OUTER JOIN qcc_categories q ON q.category_id = c.id
			WHERE order_right = order_left + 1 
			ORDER BY c.order_id",
            [$f]
        );

        $newCategories = [];
        foreach ($categories as $k => $category) {
            foreach ($this->qccCategoryKeys as $qccKey => $v) {
                foreach ($this->qccValues as $qccValue => $v2) {
                    $categories[$k]['qcc'][$qccKey][$qccValue] = ($categories[$k][$qccKey] === $qccValue) ? 1 : 0;
                }
            }
            $newCategories[$category['id']] = $categories[$k];
        }

        $s = $this->db->rawQueryOne("SELECT min(order_left) AS lft, max(order_right) AS rgt FROM catalog_categories");

        try {
            $this->db
                ->orderBy('order_left', 'asc')
                ->orderBy('order_right', 'asc');
        } catch (\Exception $exception) {
            return false;
        }

        $allCategories = $this->db
            ->where('order_left BETWEEN ? AND ?', [$s['lft'], $s['rgt']])
            ->get(
                'catalog_categories',
                null,
                [
                    'id',
                    'pid',
                    'title',
                    'IF((order_right - order_left) = 1, 1, 0) as is_leaf',
                    'order_left',
                    'order_level',
                    'order_right',
                ]
            );

        $res = [];

        foreach ($allCategories as $acategory) {
            $qccArray = [];
            foreach ($this->qccCategoryKeys as $qccKey => $v) {
                if (isset($newCategories[$acategory['id']])) {
                    $newCategories[$acategory['id']]['qcc'][$qccKey]['total'] =
                        array_sum($newCategories[$acategory['id']]['qcc'][$qccKey]);
                }
                $qccArray[$qccKey] =
                    $newCategories[$acategory['id']]['qcc'][$qccKey]
                    ?? [
                        'total' => 0,
                        'none' => 0,
                        'error' => 0,
                        'weak' => 0,
                        'overflow' => 0,
                        'normal' => 0,
                    ];
            }
            $res[] = [
                'id' => $acategory['id'],
                'pid' => $acategory['pid'],
                'is_leaf' => $acategory['is_leaf'] == 1,
                'title' => $acategory['title'],
                'qcc' => $qccArray,
            ];
        }

        foreach ($res as $category) {
            if ($category['is_leaf']) {
                $this->qccApplyCategoryToUpperNodes($res, $category['id']);
            }
        }

        foreach ($res as $k => $row) {
            foreach ($this->qccCategoryKeys as $qccKey => $qkv) {
                foreach ($this->qccValues as $qccValue => $v) {
                    $res[$k]['qcc']['perc'][$qccKey][$qccValue] = ($res[$k]['qcc'][$qccKey]['total'])
                        ? round(100 / $res[$k]['qcc'][$qccKey]['total'] * $res[$k]['qcc'][$qccKey][$qccValue])
                        : 0;
                }
            }
        }

        return $res;
    }

    public function getQccValuesZeroes() {
        $res = [];
        foreach ($this->qccValues as $k => $v) {
            $res[$k] = 0;
        }

        return $res;
    }

    public function qccApplyToUpperNodes(&$array, $id, $sum = null) {
        $id_row = array_search($id, array_column($array, 'id'));
        $pid = $array[$id_row]['pid'];
        if ($pid !== 0) {
            $pid_row = array_search($pid, array_column($array, 'id'));
            $sum = $sum ?? $array[$id_row]['qcc'];
            foreach ($this->qccKeys as $qccKey => $x) {
                if ($array[$id_row]['is_leaf']) {
                    $sum[$qccKey] = $array[$id_row]['qcc'][$qccKey];
                    $sum[$qccKey]['total'] = $array[$id_row]['qcc'][$qccKey]['total'] ?? array_sum($array[$id_row]['qcc'][$qccKey]);
                }
                foreach ($this->qccValues as $qccValue => $v) {
                    if ($sum[$qccKey][$qccValue] > 0) {
                        $array[$pid_row]['qcc'][$qccKey][$qccValue] += $sum[$qccKey][$qccValue];
                    }
                }
                $array[$pid_row]['qcc'][$qccKey]['total'] += $sum[$qccKey]['total'];
            }

            $this->qccApplyToUpperNodes($array, $pid, $sum);
        }
    }

    public function qccApplyCategoryToUpperNodes(&$array, $id, $sum = null) {
        $id_row = array_search($id, array_column($array, 'id'));
        $pid = $array[$id_row]['pid'];
        if ($pid !== 0) {
            $pid_row = array_search($pid, array_column($array, 'id'));
            if ($array[$id_row]['is_leaf']) {
                $sum = $array[$id_row]['qcc'];
            }
            foreach ($this->qccCategoryKeys as $qccKey => $x) {
                foreach ($this->qccValues as $qccValue => $v) {
                    $array[$pid_row]['qcc'][$qccKey][$qccValue] += $sum[$qccKey][$qccValue];
                }
                $array[$pid_row]['qcc'][$qccKey]['total'] += $sum[$qccKey]['total'];
            }
            $this->qccApplyCategoryToUpperNodes($array, $pid, $sum);
        }
    }

    public function makeParentChildRelations(&$inArray, &$outArray, $currentParentId = 0) {
        if (!is_array($inArray) || !is_array($outArray)) {
            return;
        }

        foreach ($inArray as $key => $tuple) {
            if ($tuple['pid'] == $currentParentId) {
                $tuple['children'] = [];
                $this->makeParentChildRelations($inArray, $tuple['children'], $tuple['id']);
                $outArray[] = $tuple;
            }
        }
    }

    public function make3DArray($inArray) {
        $newArray = [];
        $this->makeParentChildRelations($inArray, $newArray);

        return $newArray;
    }

    public function sortCategoriesTree($array) {
        if (!is_array($array)) {
            return false;
        }

        foreach ($array as $key => $id) {
            $this->db
                ->where('id', $id)
                ->update('catalog_categories', ['order_id' => $key]);
        }

        return true;
    }

    public function getCategoriesTree($id = 0, $obeyActive = false) {
        if (!is_numeric($id)) {
            return false;
        }

        $scope = ($id !== 0)
            ? $this->db->rawQueryOne('SELECT order_left, order_right FROM catalog_categories WHERE id = ?', [$id])
            : $this->db->rawQueryOne(
                'SELECT min(order_left) AS order_left, max(order_right) AS order_right '.
                'FROM catalog_categories'
            );

        $right = $scope['order_right'];
        $left = $scope['order_left'];

        if ($obeyActive) {
            $this->db->where('flag_active', 1);
        }

        $categories = $this->db
            ->where('order_left BETWEEN ? AND ?', [$left, $right])
            ->orderBy('order_id', 'asc')
            ->orderBy('order_left', 'asc')
            ->get(
                'catalog_categories',
                null,
                ['id', 'pid', 'title', 'is_leaf', 'order_left', 'order_level', 'order_right', 'flag_active']
            );

        $right = [];
        $res = [];

        if ($categories) {
            foreach ($categories as $category) {
                if (count($right) > 0) {
                    while (count($right) > 0 && ((int)$right[count($right) - 1]) < ((int)$category['order_right'])) {
                        array_pop($right);
                    }
                }

                $res[] = [
                    'id' => $category['id'],
                    'key' => $category['id'],
                    'pid' => $category['pid'],
                    'folder' => $category['is_leaf'] == 1 ? false : true,
                    'lazy' => $category['is_leaf'] == 1 ? false : true,
                    'title' => $category['title'],
                    'tooltip' => $category['title'],
                ];

                $right[] = (int)$category['order_right'];
            }
        }

        return $this->make3DArray($res);
    }

    public function searchItemIdsByArticul($term = '', $options = []) {
        return $this->db
            ->where('articul', '%'.$term.'%', 'LIKE')
            ->get('catalog_items', null, ['id']);
    }

    public function pickItem($term) {
        return $this->db
            ->where('articul', '%'.$term.'%', 'LIKE')
            ->orWhere('title', '%'.$term.'%', 'LIKE')
            ->get('catalog_items i', 20, ['id', 'title', 'articul']);
    }

    public function getItems($options = []) {

        $orders = new OrdersController();
        $cartCount = $orders->getCartCount();

        $opts = [
            'active' => isset($options['active']) ? (boolean)$options['active'] : null,
            'id' => isset($options['id']) ? $options['id'] : null,
            'articul' => isset($options['articul']) ? $options['articul'] : null,
            'order' => isset($options['order']) ? $this->order[$options['order']] : $this->order['order'],
            'dir' => (isset($options['dir']) && $options['dir'] == '-') ? 'DESC' : 'ASC',
            'brief' => isset($options['brief']) ? $options['brief'] : false,
            'new' => isset($options['new']) ? (boolean)$options['new'] : null,
            'top' => isset($options['top']) ? (boolean)$options['top'] : null,
            'qcc' => isset($options['qcc']) ? (boolean)$options['qcc'] : null,
            'special' => isset($options['special']) ? (boolean)$options['special'] : null,
            'commission' => isset($options['commission']) ? (boolean)$options['commission'] : null,
            'image' => isset($options['image']) ? (boolean)$options['image'] : true,
            'rating' => isset($options['rating']) ? (boolean)$options['rating'] : true,
            'category' => isset($options['category']) ? (int)$options['category'] : null,
            'features' => isset($options['features']) ? (boolean)$options['features'] : false,
            'sortstock' => isset($options['sortstock']) ? (boolean)$options['sortstock'] : false,
            'promo' => isset($options['promo']) ? (int)$options['promo'] : null,
        ];

        $fields = $this->catalog_items_fields;

        if (!is_null($opts['id'])) {
            if (is_array($opts['id'])) {
                $this->db->where('i.id', $opts['id'], 'IN');
            } elseif (is_numeric($opts['id'])) {
                $this->db->where('i.id', $opts['id']);
            }
        }

        if (!is_null($opts['articul'])) {
            if (is_array($opts['articul']) && count($opts['articul']) > 0) {
                $this->db->where('i.articul', $opts['articul'], 'IN');
            } elseif (is_numeric($opts['articul'])) {
                $this->db->where('i.articul', $opts['articul']);
            }
        }

        if ($opts['brief']) {
            $fields = $this->catalog_items_fields_brief;
        }

        if ($opts['category']) {
            $catlist = $this->getLeafCategoriesList((int)$opts['category']);
            if (count($catlist)) {
                $this->db
                    ->join('catalog_categories_items c', 'c.item_id = i.id', 'LEFT')
                    ->where('c.category_id', $catlist, 'IN');
            }
        }

        if ($opts['new'] !== null) {
            $this->db->where('i.flag_new', $opts['new'] ? 1 : 0);
        }

        if ($opts['special'] !== null) {
            $this->db->where('i.flag_special', $opts['special'] ? 1 : 0);
        }

        if ($opts['commission'] !== null) {
            $this->db->where('i.flag_commission', $opts['commission'] ? 1 : 0);
        }

        if ($opts['top'] !== null) {
            $this->db->where('i.flag_top', $opts['top'] ? 1 : 0);
        }

        if ($opts['active'] !== null) {
            $this->db
                ->where('i.flag_active', $opts['active'] ? 1 : 0)
                ->where('cat.flag_active', $opts['active'] ? 1 : 0);

        }

        if ($opts['image']) {
            $fields = array_merge($fields, ['g.filename']);
            $this->db
                ->join('catalog_items_images g', 'g.item_id = i.id and g.is_default = 1', 'LEFT OUTER');
        }


        $uid = $this->account->id ?? 0;
        if ($cartCount) {
            $fields = array_merge($fields, ['IF(cart.id IS NULL,0,1) AS in_cart']);
            $this->db->join('cart', "cart.item_id = i.id and cart.user_id = {$uid}", 'LEFT OUTER');
        }

        if ($opts['qcc']) {
            $fields = array_merge(
                $fields,
                [
                    'q.description as qcc_description',
                    'q.images as qcc_images',
                    'q.seo_description as qcc_seo_description',
                    'q.seo_keywords as qcc_keywords',
                    'q.features as qcc_features',
                    'q.price as qcc_price',
                    'q.articul as qcc_articul',
                ]
            );
            $this->db
                ->join('qcc_items q', 'q.item_id = i.id', 'LEFT OUTER');
        }

        if ($opts['rating']) {
            $fields = array_merge($fields, ['IFNULL(AVG(r.rating), 0) AS rating, COUNT(r.rating) AS votes']);
            $this->db
                ->join('catalog_items_reviews r', 'r.item_id = i.id', 'LEFT OUTER');
        }

        $fields = array_merge($fields, ['pr.price', 'pr.date as price_date', 'cid.category_id']);

        if ($opts['sortstock']) {
            //	$this->db->;
        }


        $fields = array_merge(
            $fields,
            [
                'pi.promo_id',
                'promo.slug as promo_slug',
                'pi.discount',
                'pi.discount_unit',
                'IF((DATE(NOW()) >= promo.date_start AND DATE(NOW()) <= promo.date_end), 1, 0) as promo_current',
            ]
        );
        $this->db
            ->join('promo_items pi', 'pi.item_id = i.id', 'LEFT OUTER')
            ->join('promo', 'promo.id = pi.promo_id', 'LEFT OUTER');


        $items = $this->db
            ->join(
                'catalog_items_prices pr',
                'pr.item_id = i.id and pr.date = '.
                '(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 WHERE pr2.item_id = i.id '.
                'GROUP BY pr2.item_id LIMIT 1)',
                'LEFT'
            )
            ->join('catalog_categories_items cid', 'cid.item_id = i.id', 'LEFT')
            ->join('catalog_categories cat', 'cid.category_id = cat.id', 'LEFT')
            ->groupBy('i.id')
            // ->orderBy('i.stock', 'ASC')
            ->orderBy($opts['order'], $opts['dir'])
            ->get('catalog_items i', null, $fields);

        $items_ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $items
        );

        if ($opts['features'] && count($items)) {
            $feature_fields = ['i.feature_value', 'f.title', 'f.unit', 'i.feature_id', 'i.id', 'i.item_id'];

            if ($opts['category']) {
                // fetch only category features
                $features = $this->db
                    ->where('category_id', $opts['category'])
                    ->get('catalog_category_features');

                if (count($features)) {
                    $feature_ids = [];
                    foreach ($features as $feature) {
                        $feature_ids[] = $feature['feature_id'];
                    }

                    $features_all = $this->db
                        ->where('i.feature_id', $feature_ids, 'IN')
                        ->where('i.item_id', $items_ids, 'IN')
                        ->join('catalog_features f', 'f.id = i.feature_id', 'LEFT')
                        ->get('catalog_items_features i', null, $feature_fields);

                    $result = [];
                    foreach ($features_all as $feature) {
                        $result[$feature['item_id']][] = [
                            'feature_title' => $feature['title'],
                            'feature_value' => $feature['feature_value'],
                            'feature_unit' => $feature['unit'],
                        ];
                    }

                    for ($i = 0; $i < count($items); $i++) {
                        if (isset($result[$items[$i]['id']])) {
                            $items[$i]['features'] = $result[$items[$i]['id']];
                        }
                    }
                }
            } else {
                $features_all = $this->db
                    ->where('i.item_id', $items_ids, 'IN')
                    ->join('catalog_features f', 'f.id = i.feature_id', 'LEFT')
                    ->get('catalog_items_features i', null, $feature_fields);

                $result = [];
                foreach ($features_all as $feature) {
                    $result[$feature['item_id']][] = [
                        'feature_title' => $feature['title'],
                        'feature_value' => $feature['feature_value'],
                        'feature_unit' => $feature['unit'],
                    ];
                }

                for ($i = 0; $i < count($items); $i++) {
                    if (isset($result[$items[$i]['id']])) {
                        $items[$i]['features'] = $result[$items[$i]['id']];
                    }
                }
            }
        }

        if ($opts['category'] && !$this->account->isAdmin()) {
            $root = $this->getRootCategory((int)$opts['category']);
            $this->increaseCategoryPopularity($root['id']);
        }

        return $items;
    }

    public function getItemsFeatures($ids) {
        $feature_fields = ['i.feature_value', 'f.title', 'f.unit', 'i.feature_id', 'i.item_id'];
        $features = $this->db
            ->where('i.item_id', $ids, 'IN')
            ->join('catalog_features f', 'f.id = i.feature_id', 'LEFT')
            ->get('catalog_items_features i', null, $feature_fields);

        $res = [];
        $used_f = [];
        foreach ($ids as $item_id) {
            $res[$item_id] = [];
            foreach ($features as $item) {
                if ($item['item_id'] == $item_id && $item['feature_id'] != 0) {
                    $res[$item_id][$item['feature_id']] = $item['feature_value'];
                    $used_f[] = (int)$item['feature_id'];
                }
            }
        }

        $used_f = array_unique($used_f);
        sort($used_f);

        foreach ($res as $item_id => $f) {
            foreach ($used_f as $fid) {
                if (isset($res[$item_id][$fid])) {
                    continue;
                }
                $res[$item_id][$fid] = '';
            }
            ksort($res[$item_id]);
        }

        $fres = [
            'list' => $used_f,
            'items' => $res,
        ];

        return $fres;
    }

    public function getItemsSEO() {
        return $this->db
            ->where('flag_active', 1)
            ->get("catalog_items", null, ['title', 'id']);
    }

    public function getCategoriesSEO() {
        return $this->db
            ->where('(order_right-order_left)=1')
            ->get("catalog_categories", null, ['title', 'id']);
    }

    public function getStockStrings() {
        return $this->stocks;
    }

    public function hasSingleChild($id) {
        $this->db
            ->where('pid', $id)
            ->get('catalog_categories');

        return $this->db->count === 1;
    }

    public function getRootCategory($id) {
        $category = $this->getCategory($id);

        return $this->db
            ->where('order_right', $category['order_right'], '>=')
            ->where('order_left', $category['order_left'], '<=')
            ->where('pid', 0)
            ->getOne('catalog_categories');
    }

    public function getCategory($id, $options = []) {
        if ($id === 0) {
            return ['id' => 0, 'pid' => 0, 'title' => '(Корневой раздел)'];
        }

        if (isset($options['active'])) {
            $this->db->where('c.flag_active', ($options['active'] ? 1 : 0));
        }

        $category = $this->db
            ->where('c.id', $id)
            ->join('catalog_category_images i', 'c.id = i.category_id', 'LEFT')
            ->getOne(
                'catalog_categories c',
                array_merge($this->catalog_categories_fields, ['i.filename', 'i.id as image_id'])
            );

        if ($this->db->count > 0) {
            if ($category['is_leaf'] == 1) {
                $category['single_child'] = $this->hasSingleChild($category['pid']);
            }

            return $category;
        } else {
            return false;
        }
    }

    public function getTopCategories($ids) {
        $query = "
			SELECT t1.id,
			(SELECT t2.id 
				FROM catalog_categories t2
				WHERE t2.order_left < t1.order_left AND t2.order_right > t1.order_right
				ORDER BY t2.order_right - t1.order_right ASC
				LIMIT 1
			) as parent_id,
			(SELECT t2.title
				FROM catalog_categories t2
				WHERE t2.order_left < t1.order_left AND t2.order_right > t1.order_right
				ORDER BY t2.order_right - t1.order_right ASC
				LIMIT 1
			) as parent_title
			FROM catalog_categories t1
			WHERE t1.id IN (?)
			ORDER BY order_right - order_left DESC";

        return $this->db->rawQuery($query, [$ids]);
    }

    public function pickParentCategoriesList($id = 0, $term = null) {
        $scope = $this->db->rawQueryOne("SELECT order_left, order_right FROM catalog_categories WHERE id = ?", [$id]);

        $right = [];
        $res = [];

        if (!empty(trim($term))) {
            $this->db->where('c.title', '%'.$term.'%', 'LIKE');
        }

        $categories = $this->db
            ->where('c.order_left', [$scope['order_left'], $scope['order_right']], 'NOT BETWEEN')
            ->where('(c.order_right - c.order_left)', 1, '>')
            ->orderBy('c.order_left', 'asc')
            ->get('catalog_categories c', null, $this->catalog_categories_fields);

        foreach ($categories as $category) {
            if (count($right) > 0) {
                while (count($right) > 0 && ((int)$right[count($right) - 1]) < ((int)$category['order_right'])) {
                    array_pop($right);
                }
            }
            $res[] = $category['id'];
            $right[] = (int)$category['order_right'];
        }

        return $res;

    }

    public function getLeafCategoriesList($id = 0, $term = null) {
        // retrieve the left and right value of the $root node
        $scope = ($id !== 0) ?
            $this->db->rawQueryOne("SELECT order_left, order_right FROM catalog_categories WHERE id = ?", [$id]) :
            $this->db->rawQueryOne("SELECT min(order_left) AS order_left, max(order_right) AS order_right FROM catalog_categories");

        // start with an empty $right stack
        $right = [];
        $res = [];

        if (!empty(trim($term))) {
            $this->db->where('c.title', '%'.$term.'%', 'LIKE');
        }

        $categories = $this->db
            ->where('(c.order_right - c.order_left)', 1)
            ->where('c.order_left', [$scope['order_left'], $scope['order_right']], 'BETWEEN')
            ->orderBy('c.order_left', 'asc')
            ->get('catalog_categories c', null, $this->catalog_categories_fields);

        foreach ($categories as $category) {
            if (count($right) > 0) {
                while (count($right) > 0 && ((int)$right[count($right) - 1]) < ((int)$category['order_right'])) {
                    array_pop($right);
                }
            }
            $res[] = $category['id'];
            $right[] = (int)$category['order_right'];
        }

        return $res;
    }

    public function hasItem($id) {
        return $this->db->where('id', $id)->has('catalog_items');
    }

    public function getItem($id, $options = []) {

        if (isset($options['active'])) {
            $this->db->where('i.flag_active', ($options['active'] ? 1 : 0));
        }

        $uid = $this->account->id ?? 0;
        $item = $this->db
            ->where('i.id', $id)
            ->join('catalog_items_reviews r', 'r.item_id = i.id', 'LEFT OUTER')
            ->join('catalog_categories_items c', 'c.item_id = i.id', 'LEFT')
            ->join('cart', "cart.item_id = i.id and cart.user_id = {$uid}", 'LEFT OUTER')
            ->join('promo_items pi', 'pi.item_id = i.id', 'LEFT OUTER')
            ->join('promo', 'promo.id = pi.promo_id', 'LEFT OUTER')
            ->join(
                'catalog_items_prices pr',
                'pr.item_id = i.id and pr.date = (SELECT MAX(pr2.date) FROM catalog_items_prices pr2 '.
                'WHERE pr2.item_id = i.id GROUP BY pr2.item_id LIMIT 1)',
                'LEFT'
            )
            ->groupBy('i.id')
            ->getOne(
                'catalog_items i',
                array_merge(
                    $this->catalog_items_fields,
                    [
                        'c.category_id',
                        'IFNULL(AVG(r.rating), 0) AS rating',
                        'IF(cart.id IS NULL,0,1) AS in_cart',
                        'COUNT(r.rating) AS votes',
                        'pr.price',
                        'pr.date as price_date',
                        'pi.promo_id',
                        'promo.slug as promo_slug',
                        'pi.discount',
                        'pi.discount_unit',
                        'IF((DATE(NOW()) >= promo.date_start AND DATE(NOW()) <= promo.date_end), 1, 0) as promo_current',
                    ]
                )
            );

        if ($item) {
            $item['features'] = $this->getItemFeatures($id);
            $item['images'] = $this->getItemImages($id);
            $item['videos'] = $this->getItemVideos($id);
            $item['related'] = $this->getRelatedItems($id);
            $item['similar'] = $this->getSimilarItems($id);
        }

        return $item;
    }

    public function getRelatedItems($id) {
        $related_ids = $this->db
            ->where('item_id', $id)
            ->get('catalog_items_related', null, ['related_id']);

        if (count($related_ids) === 0) {
            return [];
        }

        $ids = array_map(function ($el) { return $el['related_id']; }, $related_ids);

        return $this->getItems(['id' => $ids, 'brief' => true, 'active' => true]);
    }

    public function getSimilarItems($id) {
        $similar_ids = $this->db
            ->where('item_id', $id)
            ->get('catalog_items_similar', null, ['similar_id']);
        if (count($similar_ids) === 0) {
            return [];
        }
        $ids = array_map(function ($el) { return $el['similar_id']; }, $similar_ids);

        return $this->getItems(['id' => $ids, 'brief' => true, 'active' => true]);
    }

    public function updateItem($id, $data) {
        $item = [
            'title' => $data['title'],
            'seo_title' => $data['seo_title'],
            'articul' => $data['articul'],
            'description' => $data['description'],
            'seo_description' => $data['seo_description'],
            'unit' => $data['unit'],
            'stock' => $data['stock'],
            'flag_active' => isset($data['flag_active']) ? 1 : 0,
            'flag_price_warn' => isset($data['flag_price_warn']) ? 1 : 0,
            'flag_top' => isset($data['flag_top']) ? 1 : 0,
            'flag_special' => isset($data['flag_special']) ? 1 : 0,
            'flag_new' => isset($data['flag_new']) ? 1 : 0,
            'flag_commission' => isset($data['flag_commission']) ? 1 : 0,
        ];

        $this->db->where('id', $id);
        if ($this->db->update('catalog_items', $item)) {
            return $this->getItem($id);
        } else {
            return false;
        }
    }

    public function addItem($data) {
        $category_id = $data['category_id'];
        $item = [
            'title' => $data['title'],
            'articul' => $data['articul'],
            'description' => $data['description'],
            'unit' => $data['unit'],
            'stock' => $data['stock'],
            'flag_active' => isset($data['flag_active']) ? 1 : 0,
            'flag_price_warn' => isset($data['flag_price_warn']) ? 1 : 0,
        ];

        if ($this->db->insert('catalog_items', $item)) {
            $item_id = $this->db->getInsertId();
            $this->db->insert(
                'catalog_categories_items',
                [
                    'category_id' => $category_id,
                    'item_id' => $item_id,
                ]
            );

            return $item_id;
        };

        return false;
    }

    public function linkItemArticle($id, $article_id) {
        return $this->db->insert(
            'catalog_items_articles',
            [
                'article_id' => $article_id,
                'item_id' => $id,
            ]
        );
    }

    public function unlinkItemArticle($id, $article_id) {
        return $this->db
            ->where('item_id', $id)
            ->where('article_id', $article_id)
            ->delete('catalog_items_articles');
    }

    public function addItemRelated($id, $related_id) {
        return $this->db->insert('catalog_items_related', ['related_id' => $related_id, 'item_id' => $id]);
    }

    public function removeItemRelated($id, $related_id) {
        return $this->db
            ->where('item_id', $id)
            ->where('related_id', $related_id)
            ->delete('catalog_items_related');
    }

    public function addItemSimilar($id, $similar_id) {
        return $this->db->insert('catalog_items_similar', ['similar_id' => $similar_id, 'item_id' => $id]);
    }

    public function removeItemSimilar($id, $similar_id) {
        return $this->db
            ->where('item_id', $id)
            ->where('similar_id', $similar_id)
            ->delete('catalog_items_similar');
    }

    public function addItemVideo($id, $url) {
        if ($this->db->insert('catalog_items_videos', ['url' => $url, 'item_id' => $id])) {
            $video_id = $this->db->getInsertId();

            return $this->db
                ->where('id', $video_id)
                ->getOne('catalog_items_videos');
        } else {
            return false;
        }
    }

    public function deleteItemVideo($id) {
        return $this->db
            ->where('id', $id)
            ->delete('catalog_items_videos');
    }

    public function getItemImages($id) {
        return $this->db
            ->where('i.item_id', $id)
            ->orderBy('i.is_default', 'DESC')
            ->get('catalog_items_images i', null, ['i.is_default', 'i.filename', 'i.id']);
    }

    public function getItemVideos($id) {
        return $this->db
            ->where('item_id', $id)
            ->get('catalog_items_videos', null, ['url', 'id']);
    }

    public function deleteItemImage($image_id) {
        $item_id = $this->db
            ->where('id', $image_id)
            ->getValue('catalog_items_images', 'item_id');

        if ($this->db
            ->where('item_id', $item_id)
            ->where('id', $image_id)
            ->where('is_default', 1)
            ->has('catalog_items_images')) {
            $this->db
                ->where('id', $image_id)
                ->delete('catalog_items_images');

            return $this->db
                ->where('item_id', $item_id)
                ->update('catalog_items_images', ['is_default' => 1], 1);
        } else {
            return $this->db
                ->where('id', $image_id)
                ->delete('catalog_items_images');
        }
    }

    public function setDefaultItemImage($item_id, $image_id) {
        if ($this->db
            ->where('item_id', $item_id)
            ->where('id', $image_id)
            ->has('catalog_items_images')) {
            $this->db
                ->where('item_id', $item_id)
                ->update('catalog_items_images', ['is_default' => 0]);

            return $this->db
                ->where('item_id', $item_id)
                ->where('id', $image_id)
                ->update('catalog_items_images', ['is_default' => 1], 1);
        }

        return false;
    }

    public function addItemImage($item_id, $filename) {
        $has_default = $this->db
            ->where('item_id', $item_id)
            ->where('is_default', 1)
            ->has('catalog_items_images');

        return $this->db->insert(
            'catalog_items_images',
            [
                'item_id' => $item_id,
                'filename' => $filename,
                'is_default' => ($has_default ? 0 : 1),
            ]
        );
    }

    public function getItemFeatures($id) {
        /* TODO: specify recieving fields */
        return $this->db
            ->join('catalog_features f', 'f.id = i.feature_id')
            ->where('i.item_id', $id)
            ->groupBy('i.feature_id')
            ->get('catalog_items_features i');
    }

    public function getItemArticles($id) {
        /* TODO: specify recieving fields */
        return $this->db
            ->join('content a', 'a.id = i.article_id')
            ->where('i.item_id', $id)
            ->get('catalog_items_articles i', null, ['a.title', 'a.slug', 'i.article_id', 'i.item_id']);
    }


    public function updateItemFeatures($id, $features) {
        if ($features && $id) {
            $this->db
                ->where('item_id', $id)
                ->delete('catalog_items_features');

            $insertData = [];
            foreach ($features as $fid => $value) {
                $insertData[] = [
                    'item_id' => $id,
                    'feature_id' => $fid,
                    'feature_value' => $value,
                ];
            }

            if (count($insertData)) {
                $this->db->insertMulti('catalog_items_features', $insertData);
            }
        }
    }

    public function cleanUpCategoryTree() {
        $ids = $this->db->rawQuery(
            "
			SELECT t1.id AS l1, t2.id AS l2, t3.id AS l3, t4.id AS l4, t5.id AS l5, t6.id AS l6, t7.id AS l7
			FROM catalog_categories AS t1
			LEFT JOIN catalog_categories AS t2 ON t2.pid = t1.id
			LEFT JOIN catalog_categories AS t3 ON t3.pid = t2.id
			LEFT JOIN catalog_categories AS t4 ON t4.pid = t3.id
			LEFT JOIN catalog_categories AS t5 ON t5.pid = t4.id
			LEFT JOIN catalog_categories AS t6 ON t6.pid = t5.id
			LEFT JOIN catalog_categories AS t7 ON t7.pid = t6.id
			WHERE t1.pid = 0"
        );
        $flat = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($ids));
        $res = [];
        foreach ($flat as $v) {
            $res[] = $v;
        }
        $res = array_filter(
            array_unique($res),
            function ($var) {
                return !is_null($var);
            }
        );
        $ids = implode(',', $res);
        $this->db->rawQuery("DELETE FROM catalog_categories_items_count WHERE category_id NOT IN ({$ids})");
        $this->db->rawQuery("DELETE FROM catalog_categories_items WHERE category_id NOT IN ({$ids})");
        $this->db->rawQuery("DELETE FROM catalog_category_features WHERE category_id NOT IN ({$ids})");
        $this->db->rawQuery("DELETE FROM catalog_category_images WHERE category_id NOT IN ({$ids})");
        $this->db->rawQuery("DELETE FROM catalog_category_keywords WHERE category_id NOT IN ({$ids})");
        $this->db->rawQuery("DELETE FROM catalog_categories WHERE id NOT IN ({$ids})");
    }

    public function repairTree() {
        $this->db->rawQuery("CALL recover_tree");
    }

    /**
     * Get array of keywords
     *
     * @param       $id
     * @param array $params
     *        query: search keyword by string
     *
     * @return array
     * @throws \Exception
     */
    public function getCategoryKeywords($id = null, $params = []) {
        $this->db->join('catalog_category_keywords ck', 'ck.keyword_id = k.id', 'LEFT');

        if (!empty($id)) {
            $this->db->where('ck.category_id', $id);
        }

        if (isset($params['query']) && !empty($params['query'])) {
            $this->db->where('k.keyword', '%'.$params['query'].'%', 'LIKE');
        }

        $keywords = $this->db->get('keywords k', null, ['k.keyword', 'ck.keyword_id', 'ck.category_id']);

        return $keywords;
    }

    /**
     * Get array of keywords
     *
     * @param       $id
     * @param array $params
     *        query: search keyword by string
     *
     * @return array
     */
    public function getItemKeywords($id = null, $params = []) {
        $this->db->join('catalog_items_keywords ik', 'ik.keyword_id = k.id', 'LEFT');

        if (!empty($id)) {
            $this->db->where('ik.item_id', $id);
        }

        if (isset($params['query']) && !empty($params['query'])) {
            $this->db->where('k.keyword', '%'.$params['query'].'%', 'LIKE');
        }

        $keywords = $this->db->get('keywords k', null, ['k.keyword', 'ik.keyword_id', 'ik.item_id']);

        return $keywords;
    }


    public function setCategoryImage($id, $filename) {
        if ($this->db->where('category_id', $id)->has('catalog_category_images')) {
            $this->db
                ->where('category_id', $id)
                ->update('catalog_category_images', ['filename' => $filename]);
        } else {
            $this->db
                ->insert('catalog_category_images', ['filename' => $filename, 'category_id' => $id]);
        }
    }

    public function removeCategoryImage($id) {
        $filename = $this->db
            ->where('category_id', $id)
            ->getValue('catalog_category_images', 'filename');
        if ($filename) {
            $fullpath = $_SERVER['DOCUMENT_ROOT'].Helpers::getMediaCachePath($filename);
            try {
                unlink($fullpath);

                return $this->db
                    ->where('category_id', $id)
                    ->delete('catalog_category_images');
            } catch (\Exception $exception) {
                //
                return false;
            }
        }

        return false;
    }

    public function deleteItemFeature($item_id, $feature_id) {
        return $this->db
            ->where('item_id', $item_id)
            ->where('feature_id', $feature_id)
            ->delete('catalog_items_features');
    }

    public function getCategoryFeatures($id) {
        return $this->db
            ->where('f.category_id', $id)
            ->join('catalog_features fa', 'fa.id = f.feature_id')
            ->orderBy('f.feature_order')
            ->get('catalog_category_features f', null, ['fa.title', 'fa.unit', 'fa.id', 'f.marked']);
    }

    public function getFeatures($params = []) {
        $fields = ['f.title', 'f.unit', 'f.id', 'f.type'];
        $opts = [
            'page' => isset($params['page']) ? $params['page'] : null,
            'usage' => isset($params['usage']) ? $params['usage'] : false,
            'pagerWidth' => isset($params['pagerWidth']) ? $params['pagerWidth'] : 10,
            'limit' => isset($params['limit']) ? $params['limit'] : null,
            'id' => isset($params['id']) ? $params['id'] : null,
            'query' => isset($params['query']) ? $params['query'] : null,
        ];

        if (!empty(trim($opts['query']))) {
            $this->db->where('f.title', '%'.trim($opts['query']).'%', 'LIKE');
        }

        $this->db
            ->orderBy('f.title', "ASC");

        if ($opts['usage']) {
            $fields = array_merge(
                $fields,
                [
                    'COUNT(distinct cf.category_id) as categories',
                    'COUNT(distinct itf.item_id) as items',
                ]
            );
            $this->db
                ->groupBy('f.id')
                ->join('catalog_category_features cf', 'f.id = cf.feature_id', 'LEFT')
                ->join('catalog_items_features itf', 'f.id = itf.feature_id', 'LEFT');
        }


        if (!is_null($opts['page'])) {
            $page = $opts['page'];
            $pagerWidth = $opts['pagerWidth'];

            $features = $this->db
                ->arraybuilder()
                ->paginate("catalog_features f", $opts['page'], $fields);

            $pagerStart =
                ($page - abs($pagerWidth / 2)) < 0
                    ? 0 : (($page + ceil($pagerWidth / 2)) > $this->db->totalPages
                    ? $this->db->totalPages - $pagerWidth
                    : $page - abs($pagerWidth / 2));

            return [
                'items' => $features,
                'pages' => $this->db->totalPages,
                'page' => $opts['page'],
                'pagerStart' => $pagerStart,
                'pagerEnd' => $this->db->totalPages > $pagerWidth ? $pagerStart + $pagerWidth : $this->db->totalPages,
                'query' => $opts['query'],
                'count' => $this->db->totalCount,
            ];
        } else {
            return $this->db->get('catalog_features f', $opts['limit'], $fields);
        }
    }

    public function getFeature($id) {
        $fields = [
            'f.title',
            'f.unit',
            'f.id',
            'f.type',
            'COUNT(distinct cf.category_id) as categories',
            'COUNT(distinct itf.item_id) as items',
        ];

        $f = $this->db
            ->where('f.id', $id)
            ->join('catalog_category_features cf', 'f.id = cf.feature_id', 'LEFT')
            ->join('catalog_items_features itf', 'f.id = itf.feature_id', 'LEFT')
            ->groupBy('f.id')
            ->getOne('catalog_features f', $fields);

        return $f;
    }

    public function getFeaturesTypes() {
        return $this->featureTypes;
    }

    public function deleteFeature($id) {
        return $this->db
            ->where('id', $id)
            ->delete('catalog_features');
    }

    public function addFeature($data) {
        if (!in_array($data['type'], array_keys($this->getFeaturesTypes()))) {
            return false;
        }
        $feature = [
            'title' => $data['title'],
            'unit' => $data['unit'],
            'type' => $data['type'],
        ];

        if ($this->db->insert('catalog_features', $feature)) {
            return $this->db->getInsertId();
        };

        return false;
    }

    public function updateFeature($id, $data) {
        if (!in_array($data['type'], array_keys($this->getFeaturesTypes())) || !is_numeric($id)) {
            return false;
        }
        $feature = [
            'title' => $data['title'],
            'unit' => $data['unit'],
            'type' => $data['type'],
        ];

        return $this->db
            ->where('id', $id)
            ->update('catalog_features', $feature);
    }

    public function getFeaturesArray() {

        $features = $this->db
            ->orderBy('title', "ASC")
            ->get('catalog_features ', null, ['title', 'unit', 'id']);

        $res = [];
        foreach ($features as $feature) {
            $res[$feature['id']] = [
                'title' => $feature['title'],
                'unit' => $feature['unit'],
            ];
        }

        return $res;
    }

    public function updateCategory($id, $data) {
        $categoryData = [
            'title' => trim($data['title']),
            'seo_title' => trim($data['seo_title']),
            'pid' => $data['pid'],
            'description' => $data['description'],
            'seo_description' => $data['seo_description'],
            'appearance' => isset($data['appearance']) ? $data['appearance'] : 'icons',
            'flag_active' => isset($data['flag_active']) ? 1 : 0,
        ];

        return $this->db
            ->where('id', $id)
            ->update('catalog_categories', $categoryData);
    }

    public function removeCategoryFeatures($id, $featuresArray = []) {
        if (!empty($featuresArray)) {
            $this->db->where('feature_id', $featuresArray, 'IN');
        }

        return $this->db
            ->where('category_id', $id)
            ->delete('catalog_category_features');
    }

    public function addCategoryFeatures($id, $featuresArray) {
        $index = 0;
        $features = array_map(
            function ($feature) use ($id, &$index) {
                return [
                    'category_id' => $id,
                    'feature_id' => $feature,
                    'feature_order' => ++$index,
                ];
            },
            $featuresArray
        );

        return $this->db->insertMulti('catalog_category_features', $features);
    }

    public function removeCategoryKeywords($id, $keywordsArray = []) {
        if (!empty($keywordsArray)) {
            $this->db->where('keyword_id', $keywordsArray, 'IN');
        }

        return $this->db
            ->where('category_id', $id)
            ->delete('catalog_category_keywords');
    }

    public function removeItemKeywords($id, $keywordsArray = []) {
        if (!empty($keywordsArray)) {
            $this->db->where('keyword_id', $keywordsArray, 'IN');
        }

        return $this->db
            ->where('item_id', $id)
            ->delete('catalog_items_keywords');
    }

    public function addCategoryKeywords($id, $keywordsArray) {
        $data = [];
        $keywords = new KeywordsController();
        foreach ($keywordsArray as $keyword) {
            $data[] = [
                'category_id' => $id,
                'keyword_id' => is_numeric($keyword) ? (int)$keyword : (int)$keywords->add($keyword),
            ];
        }

        return $this->db->insertMulti('catalog_category_keywords', $data);
    }

    public function addItemsKeywords($id, $keywordsArray) {
        $data = [];
        $keywords = new KeywordsController();
        foreach ($keywordsArray as $keyword) {
            $data[] = [
                'item_id' => $id,
                'keyword_id' => is_numeric($keyword) ? (int)$keyword : (int)$keywords->add($keyword),
            ];
        }

        return $this->db->insertMulti('catalog_items_keywords', $data);
    }

    public function isLeafCategory($id) {
        return $this->db
            ->where('order_right - order_left', 1)
            ->where('id', $id)
            ->has('catalog_categories');
    }

    public function updateItemsOrder($items) {
        foreach ($items as $key => $item) {
            $this->db
                ->where('id', $item)
                ->update('catalog_items', ['order_id' => $key]);
        }

        return true;
    }

    public function updatePrices($prices) {
        $data = [];
        foreach ($prices as $id => $price) {
            $data[] = [
                'price' => $price,
                'item_id' => $id,
            ];
        };

        return $this->db->insertMulti('catalog_items_prices', $data);
    }

    public function setItemStock($item, $stock) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['stock' => $stock]);
    }

    public function setItemActive($item, $active) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_active' => $active]);
    }

    public function setItemPriceWarn($item, $flag) {
        $res = $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_price_warn' => $flag]);

        return $res;
    }

    public function setItemFlagNew($item, $flag) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_new' => $flag]);
    }

    public function setItemFlagSpecial($item, $flag) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_special' => $flag]);
    }

    public function setItemFlagTop($item, $flag) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_top' => $flag]);
    }

    public function setItemFlagCommission($item, $flag) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->update('catalog_items', ['flag_commission' => $flag]);
    }

    public function deleteItem($item) {
        return $this->db
            ->where('id', $item, (is_array($item) ? 'IN' : '='))
            ->delete('catalog_items');
    }

    public function setItemCategory($id, $category_id) {

        if ($this->db
            ->where('item_id', $id)
            ->has('catalog_categories_items')) {
            return $this->db
                ->where('item_id', $id)
                ->update('catalog_categories_items', ['category_id' => $category_id]);
        } else {
            return $this->db
                ->insert(
                    'catalog_categories_items',
                    [
                        'item_id' => $id,
                        'category_id' => $category_id,
                    ]
                );
        }
    }

    public function updateTitles($titles) {
        foreach ($titles as $id => $title) {
            $this->db
                ->where('id', $id)
                ->update('catalog_items', ['title' => $title]);
        };
    }

    public function addCategory($title, $parent = 0) {
        if ($parent > 0) {
            $pc = $this->getCategory($parent);
        } else {
            $pc = $this->db->getOne('catalog_categories', ['MAX(order_right) as order_right']);
            $pc['order_level'] = 0;
            $pc['is_leaf'] = 0;
        }

        $newCategory = [
            'title' => $title,
            'pid' => $parent,
            'order_left' => $pc['order_right'],
            'order_right' => $pc['order_right'] + 1,
            'order_level' => $pc['order_level'] + 1,
            'tag' => Helpers::getSlug($title),
        ];

        $this->db->rawQuery("UPDATE catalog_categories SET order_right = order_right + 2 WHERE  order_right >= ?", [$pc['order_right']]);
        $this->db->rawQuery("UPDATE catalog_categories SET order_left = order_left + 2 WHERE  order_left > ? ", [$pc['order_right']]);

        $id = $this->db->insert('catalog_categories', $newCategory);

        if (is_numeric($id)) {
            if ($pc['is_leaf'] == 1) {
                $this->db->rawQuery("UPDATE catalog_categories_items SET category_id = ? WHERE category_id = ? ", [$id, $pc['id']]);
            }
        }

        return $id;
    }

    public function removeCategory($id) {
        $tc = $this->getCategory($id);

        if ($tc['order_right'] - $tc['order_left'] === 1) {
            // only a single category
            $this->db->rawQuery("DELETE FROM catalog_categories_items_count WHERE category_id = ?", [$id]);
            $this->db->rawQuery("DELETE FROM catalog_categories_items WHERE category_id = ?", [$id]);
            $this->db->rawQuery("DELETE FROM catalog_categories WHERE id = ?", [$id]);
        } else {
            // there is a lot of sub
            $children = $this->db->rawQuery(
                "SELECT id FROM catalog_categories WHERE order_left BETWEEN ? AND ?",
                [$tc['order_left'], $tc['order_right']]
            );
            $ids = array_map(function ($el) { return $el['id']; }, $children);
            if (count($ids)) {
                $this->db->rawQuery("DELETE FROM catalog_categories_items_count WHERE category_id IN (?)", [implode(',', $ids)]);
                $this->db->rawQuery("DELETE FROM catalog_categories_items WHERE category_id IN (?)", [implode(',', $ids)]);
                $this->db->rawQuery("DELETE FROM catalog_categories WHERE id IN (?)", [implode(',', $ids)]);
            }
        }

        // purge all unlinked items
        $this->db->rawQuery("DELETE FROM catalog_items WHERE id NOT IN (SELECT item_id FROM catalog_categories_items)");

        // align all the left/right values, shifted by width
        $width = $tc['order_right'] - $tc['order_left'] + 1;
        $this->db->rawQuery(
            "UPDATE catalog_categories SET order_right = order_right - ? WHERE order_right > ?",
            [$width, $tc['order_right']]
        );
        $this->db->rawQuery(
            "UPDATE catalog_categories SET order_left = order_left - ? WHERE  order_left > ? ",
            [$width, $tc['order_right']]
        );

        // $this->db->rawQuery("CALL recover_tree()");
        return $tc['pid'];
    }
}
