<?php

namespace odissey;

class ContentController extends Controller
{

    const CONTENT_NEWS = 'news';

    const CONTENT_ARTICLE = 'article';

    const CONTENT_SYSTEM = 'system';

    const CONTENT_CONTENT = 'content';

    private $contentTypes = [self::CONTENT_CONTENT, self::CONTENT_NEWS, self::CONTENT_ARTICLE, self::CONTENT_SYSTEM];

    public function __construct() {
        parent::__construct();
    }

    public function add($data) {
        $allowedTypes = [
            self::CONTENT_NEWS,
            self::CONTENT_ARTICLE,
            self::CONTENT_CONTENT,
            self::CONTENT_SYSTEM,
        ];

        $normalizedData = [
            'title' => $data['title'],
            'text' => $data['text'],
            'slug' => $data['slug'],
            'type' => isset($data['type']) && in_array($data['type'], $allowedTypes)
                ? $data['type']
                : self::CONTENT_NEWS,
            'publish' => (isset($data['publish']) && !empty($data['publish']))
                ? date('Y-m-d H:i:s', strtotime($data['publish']))
                : date('Y-m-d H:i:s'),
            'seo_description' => $data['seo_description'],
            'flag_active' => isset($data['flag_active']) && $data['flag_active'] === 'on' ? 1 : 0,
            'flag_protected' => isset($data['flag_protected']) && $data['flag_protected'] === 'on' ? 1 : 0,
        ];

        $this->db->insert('content', $normalizedData);

        return $this->db->getInsertId();
    }

    public function update($id, $data) {

        $normalizedData = [
            'title' => $data['title'],
            'text' => $data['text'],
            'slug' => $data['slug'],
            'type' => isset($data['type']) && in_array($data['type'], $this->contentTypes)
                ? $data['type']
                : self::CONTENT_NEWS,
            'publish' => isset($data['publish'])
                ? date('Y-m-d H:i:s', strtotime($data['publish']))
                : date('Y-m-d H:i:s'),
            'seo_description' => $data['seo_description'],
            'flag_active' => isset($data['flag_active']) && $data['flag_active'] == 'on' ? 1 : 0,
            'flag_protected' => isset($data['flag_protected']) && $data['flag_protected'] == 'on' ? 1 : 0,
        ];

        return $this->db
            ->where('id', $id)
            ->update('content', $normalizedData);
    }

    public function getList($params = []) {
        $opts = [
            'active' => isset($params['active']) ? $params['active'] : null,
            'id' => isset($params['id']) ? $params['id'] : null,
            'order' => isset($params['order']) ? $params['order'] : 'created',
            'type' => isset($params['type']) ? $params['type'] : null,
            'published' => isset($params['published']) ? $params['published'] : null,
            'limit' => isset($params['limit']) ? $params['limit'] : null,
            'query' => isset($params['query']) ? $params['query'] : null,
        ];

        if ($opts['active'] !== null) {
            $this->db->where('flag_active', ($opts['active']) ? 1 : 0);
        }

        if ($opts['published'] !== null) {
            if ($opts['published'] == 0) {
                $this->db->where('publish > NOW()');
            }
            if ($opts['published'] == 1) {
                $this->db->where('publish <= NOW()');
            }
        }

        if (!empty(trim($opts['query']))) {
            $this->db->where('title', '%'.trim($opts['query']).'%', 'LIKE');
        }

        if ($opts['id'] !== null) {
            $this->db->where('id', $opts['id']);
        }

        if ($opts['type'] !== null) {
            if (is_array($opts['type'])) {
                $this->db->where('type', $opts['type'], 'IN');
            } else {
                $this->db->where('type', $opts['type']);
            }
        }

        return $this->db
            ->orderBy($opts['order'])
            ->get('content', ($opts['limit'] !== null) ? $opts['limit'] : null);
    }

    public function getFeedSEO() {
        return $this->db
            ->where('flag_active', 1)
            ->where('type', ['news', 'article'], 'IN')
            ->get("content", null, ['title', 'id']);
    }

    public function get($id) {
        return $this->db
            ->where('id', $id)
            ->getOne('content');
    }

    public function delete($id) {
        return $this->db
            ->where('id', $id)
            ->delete('content');
    }

    public function getCatetoryLinks($id) {
        return $this->db
            ->where('content_id ', $id)
            ->join('catalog_categories c', 'c.id = cc.category_id', 'LEFT')
            ->get('content_categories cc', null, ['c.id', 'c.title']);
    }

    public function getCategoryContent($id) {
        $content = $this->db
            ->where('cc.category_id ', $id)
            ->where('c.publish <= NOW() ')
            ->join('content_categories cc', 'c.id = cc.content_id', 'LEFT')
            ->get('content c', null, ['c.id', 'c.title', 'c.type', 'c.publish']);

        return $content;
    }

    /**
     * @param int $id
     * @param int|array $categories
     *
     * @return bool
     */
    public function linkCategory($id, $categories) {
        if (is_array($categories)) {
            $data = array_map(
                function ($cat) use ($id) {
                    return [
                        'content_id' => $id,
                        'category_id' => $cat,
                    ];
                },
                $categories
            );

            return $this->db
                ->insertMulti('content_categories', $data);
        } else {
            return $this->db
                ->insert('content_categories', ['content_id' => $id, 'category_id' => $categories]);
        }
    }

    public function removeCategories($id) {
        return $this->db
            ->where('content_id', $id)
            ->delete('content_categories');
    }


    public function getKeywords($id) {
        return $this->db
            ->where('c.content_id', $id)
            ->join('content_keywords c', 'c.keyword_id = k.id', 'LEFT')
            ->get('keywords k', null, ['k.id', 'k.keyword']);
    }

    public function removeKeywords($id) {
        return $this->db
            ->where('content_id', $id)
            ->delete('content_keywords');
    }

    public function addKeywords($id, $keywordsArray) {
        $data = [];
        $keywords = new KeywordsController();
        foreach ($keywordsArray as $keyword) {
            $data[] = [
                'content_id' => $id,
                'keyword_id' => is_numeric($keyword) ? $keyword : $keywords->add($keyword),
            ];
        }

        return $this->db->insertMulti('content_keywords', $data);
    }
}
