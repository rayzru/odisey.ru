<?php

namespace odissey;

class PromoController extends Controller
{

    public static $fields = [
        'p.title',
        'p.id',
        'p.description',
        'p.date_start',
        'p.date_end',
        'p.active',
        'p.seo_title',
        'p.seo_description',
        'p.slider_id',
        'p.priority',
        'p.slug',
    ];

    public function __construct() {
        parent::__construct();
    }

    public function getPromoCount() {
        return $this->db
            ->where('(DATE(NOW()) >= date_start AND DATE(NOW()) <= date_end)')
            ->where('active', 1)
            ->getValue('promo', 'count(*)');
    }

    public function getPromoItems($id) {

        $this->db
            ->join('promo_items p', 'i.id = p.item_id', 'RIGHT OUTER')
            ->join('promo', 'p.promo_id = promo.id')
            ->join(
                'catalog_items_prices pr',
                'pr.item_id = i.id and pr.date = '.
                '(SELECT MAX(pr2.date) FROM catalog_items_prices pr2 WHERE pr2.item_id = i.id '.
                'GROUP BY pr2.item_id LIMIT 1)',
                'LEFT'
            )
            ->join('catalog_items_images g', 'g.item_id = i.id and g.is_default = 1', 'LEFT OUTER')
            ->join('catalog_items_reviews r', 'r.item_id = i.id', 'LEFT OUTER')
            ->groupBy('i.id')
            ->where('promo.id', $id);

        if (!empty($user_id)) {
            $this->db->where('orders.user_id', $user_id);
        }


        return $this->db->get(
            'catalog_items i',
            null,
            [
                'i.title',
                'i.id',
                'g.filename',
                'p.discount',
                'p.discount_unit',
                'pr.price',
                'pr.date as price_date',
                'i.articul',
                'i.stock',
                'i.unit',
                'IFNULL(AVG(r.rating), 0) AS rating',
                'COUNT(r.rating) AS votes'
            ]
        );
    }

    public function getPromo($options = []) {

        $opts = [
            'active' => isset($options['active']) ? (boolean)$options['active'] : null,
            'id' => isset($options['id']) ? $options['id'] : null,
            'current' => isset($options['current']) ? $options['current'] : null
        ];


        if (isset($opts['id'])) {
            $promo = $this->db
                ->where('p.id', $opts['id'])
                ->getOne('promo p', PromoController::$fields);

            $promo['items'] = $this->getPromoItems($opts['id']);
            return $promo;
        }

        if (isset($opts['active'])) {
            $this->db->where('active', $opts['active']);
        }

        if (isset($opts['current'])) {
            $this->db->where('current', $opts['current']);
        }

        $this->db
            ->orderBy('current', 'DESC')
            ->orderBy('p.date_end', 'DESC')
            ->join('promo_items i', 'p.id = i.promo_id', 'LEFT OUTER')
            ->groupBy('p.id');

        $fields = array_merge(PromoController::$fields, ['COUNT(i.item_id) as items_count']);
        $fields = array_merge($fields, ['IF((DATE(NOW()) >= p.date_start AND DATE(NOW()) <= p.date_end), 1, 0) as current']);

        return $this->db->get('promo p', null, $fields);
    }

    public function validateData($data) {
        $factory = new Validation();

        $rules = [
            'title' => ['required', 'max:255'],
            'date_start' => ['required', 'date_format:"d.m.Y"', 'before_or_equal:date_end'],
            'date_end' => ['required', 'date_format:"d.m.Y"', 'after_or_equal:date_start'],
            'slug' => ['required', 'max:1000'],
        ];
        $messages = [
            'title.required' => 'Обязательное поле',
            'title.max' => 'Максимальная длина 255 символов',
            'slug.required' => 'Обязательное поле',
            'slug.max' => 'Максимальная длина 1000 символов',
            'date_start.required' => 'Обязательное поле',
            'date_start.date_format' => 'Неверный формат даты',
            'date_start.before_or_equal' => 'Стартовая дата не должна позже даты окончания',
            'date_end.required' => 'Обязательное поле',
            'date_end.date_format' => 'Неверный формат даты',
            'date_end.after_or_equal' => 'Дата окончания не должна быть раньше даты начала',
        ];

        return $factory->make($data, $rules, $messages);

    }

    public function normalizeData($data) {
        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'seo_title' => $data['seo_title'],
            'description' => $data['description'],
            'date_start' => isset($data['date_start']) ? Helpers::dmy2ymd($data['date_start']) : date('Y-m-d'),
            'date_end' => isset($data['date_end']) ? Helpers::dmy2ymd($data['date_end']) : date('Y-m-d', strtotime(' + 7 days')),
            'seo_description' => $data['seo_description'],
            'active' => isset($data['active']) ? 1 : 0,
        ];
    }

    public function normalizeItemData($data) {
        return [
            'item_id' => $data['item_id'],
            'promo_id' => $data['promo_id'],
            'discount' => isset($data['discount']) ? $data['discount'] : 0,
            'discount_unit' => isset($data['discount_unit']) ? $data['discount_unit'] : 'percent',
        ];
    }

    public function add($data) {
        $item = $this->normalizeData($data);
        if ($this->db->insert('promo', $item)) {
            $id = $this->db->getInsertId();

            return $id;
        }

        return false;
    }


    public function update($id, $data) {
        $item = $this->normalizeData($data);
        $this->db->where('p.id', $id);
        if ($this->db->update('promo p', $item)) {
            return $this->getPromo(['id' => $id]);
        } else {
            return false;
        }
    }

    public function addItem($data) {
        $catalog = new CatalogController();
        $item = $this->normalizeItemData($data);

        if ($this->db->insert('promo_items', $item)) {
            return $catalog->getItem($item['item_id']);
        }

        return false;
    }

    public function removeItem($promo_id, $item_id) {
        return $this->db
            ->where('promo_id', $promo_id)
            ->where('item_id', $item_id)
            ->delete('promo_items');
    }

    public function removeKeywords($id, $keywordsArray = []) {
        if (!empty($keywordsArray)) {
            $this->db->where('keyword_id', $keywordsArray, 'IN');
        }

        return $this->db
            ->where('promo_id', $id)
            ->delete('promo_keywords');
    }

    public function addKeywords($id, $keywordsArray) {
        $data = [];
        $keywords = new KeywordsController();
        foreach ($keywordsArray as $keyword) {
            $data[] = [
                'promo_id' => $id,
                'keyword_id' => is_numeric($keyword) ? (int)$keyword : (int)$keywords->add($keyword),
            ];
        }

        return $this->db->insertMulti('promo_keywords', $data);
    }

    public function deletePromo($id) {
        return
            $this->db->where('promo_id', $id)->delete('promo_items')
            && $this->db->where('id', $id)->delete('promo');
    }

    public function getPromoKeywords($id) {
        return $this->db
            ->where('c.promo_id', $id)
            ->join('promo_keywords c', 'c.keyword_id = k.id', 'LEFT')
            ->get('keywords k', null, ['k.id', 'k.keyword']);
    }
}
