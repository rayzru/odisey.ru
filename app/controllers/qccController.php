<?php

namespace odissey;

class QccController extends Controller
{

    public const QCC_ITEM_TITLE = 1;

    public const QCC_ITEM_PRICE_WEAK = 2;

    public const QCC_ITEM_PRICE_NONE = 3;

    public function __construct() {
        parent::__construct();
    }

    public function initData() {
        $this->db->rawQuery("DELETE FROM qcc_items");
        $this->db->rawQuery("DELETE FROM qcc_categories");
        $this->db->rawQuery("INSERT INTO qcc_items (item_id) SELECT id FROM catalog_items");
        $this->db->rawQuery("INSERT INTO qcc_categories (category_id) SELECT id FROM catalog_categories");
    }

    public function checkItemsPrices() {
        // Update weak if Price is 0
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_prices ip ON ip.item_id = i.id
			WHERE ip.price = 0
			GROUP BY i.id  ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.price = 'weak'"
        );

        // Null prices
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_prices ip ON ip.item_id = i.id
			WHERE ip.price IS NULL 
			GROUP BY i.id  ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.price = 'none'"
        );

        // Normal prices
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_prices ip ON ip.item_id = i.id
			WHERE ip.price > 0 
			GROUP BY i.id  ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.price = 'normal'"
        );
    }

    public function checkItemsDescription() {

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE TRIM(i.description) = '' OR i.description IS NULL  
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.description = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.description) < 20 AND CHAR_LENGTH(i.description) > 0
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.description = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.description) >= 20 AND CHAR_LENGTH(i.description) <= 3000
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.description = 'normal'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.description) > 3000
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.description = 'overflow'"
        );
    }

    public function checkItemsSeoDescription() {

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE TRIM(i.seo_description) = '' OR i.seo_description IS NULL  
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_description = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.seo_description) < 20 AND CHAR_LENGTH(i.seo_description) > 0
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_description = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.seo_description) >= 20 AND CHAR_LENGTH(i.seo_description) <= 200
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_description = 'normal'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE CHAR_LENGTH(i.seo_description) >= 200 
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_description = 'overflow'"
        );
    }


    public function checkItemsSeoKeywords() {
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_keywords ik ON ik.item_id = i.id
			LEFT OUTER JOIN keywords k ON ik.keyword_id = k.id
			GROUP BY i.id HAVING GROUP_CONCAT(k.keyword) IS NULL OR CHAR_LENGTH(GROUP_CONCAT(k.keyword)) = 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_keywords = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_keywords ik ON ik.item_id = i.id
			LEFT OUTER JOIN keywords k ON ik.keyword_id = k.id
			GROUP BY i.id HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 200 
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_keywords = 'overflow'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_keywords ik ON ik.item_id = i.id
			LEFT OUTER JOIN keywords k ON ik.keyword_id = k.id
			GROUP BY i.id HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) < 20 AND CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 0 
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_keywords = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_keywords ik ON ik.item_id = i.id
			LEFT OUTER JOIN keywords k ON ik.keyword_id = k.id
			GROUP BY i.id 
			HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 20 AND CHAR_LENGTH(GROUP_CONCAT(k.keyword)) <= 200 
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.seo_keywords = 'normal'"
        );
    }

    public function checkItemsImages() {
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_images im ON im.item_id = i.id
			GROUP BY i.id HAVING COUNT(im.item_id) = 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.images = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_images im ON im.item_id = i.id
			GROUP BY i.id HAVING COUNT(im.item_id) > 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.images = 'normal'"
        );
    }

    public function checkItemsFeatures() {
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_categories_items ci ON ci.item_id = i.id
			LEFT OUTER JOIN catalog_categories c ON c.id = ci.category_id
			LEFT OUTER JOIN catalog_items_features fi ON fi.item_id = i.id
			LEFT OUTER JOIN catalog_features f ON fi.feature_id = f.id
			LEFT OUTER JOIN catalog_category_features cf ON cf.category_id = c.id
			GROUP BY i.id 
			HAVING  COUNT(DISTINCT cf.id) > COUNT(DISTINCT fi.id) AND COUNT(DISTINCT fi.id) > 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.features = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi  JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_categories_items ci ON ci.item_id = i.id
			LEFT OUTER JOIN catalog_categories c ON c.id = ci.category_id
			LEFT OUTER JOIN catalog_items_features fi ON fi.item_id = i.id
			LEFT OUTER JOIN catalog_features f ON fi.feature_id = f.id
			LEFT OUTER JOIN catalog_category_features cf ON cf.category_id = c.id
			GROUP BY i.id 
			HAVING  COUNT(DISTINCT cf.id) <= COUNT(DISTINCT fi.id) AND COUNT(DISTINCT fi.id) > 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.features = 'normal'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			LEFT OUTER JOIN catalog_items_features fi ON fi.item_id = i.id
			GROUP BY i.id 
			HAVING COUNT(DISTINCT fi.id) = 0
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.features = 'none'"
        );
    }

    public function checkItemsArticul() {
        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE i.articul IS NULL OR TRIM(i.articul) = ''  
			GROUP BY i.id 
			ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.articul = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE i.articul NOT REGEXP '^[A-Z]{2}[0-9]{7}' 
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.articul = 'error'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_items qi JOIN (
			SELECT DISTINCT i.id FROM qcc_items qi 
			LEFT JOIN catalog_items i ON qi.item_id = i.id
			WHERE i.articul REGEXP '^[A-Z]{2}[0-9]{7}' 
			GROUP BY i.id ORDER BY i.id ASC
		) i ON qi.item_id = i.id SET qi.articul = 'normal'"
        );
    }

    public function checkCategoriesDescription() {
        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE TRIM(c.description) = '' OR c.description IS NULL  
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.description = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE CHAR_LENGTH(c.description) < 20 AND CHAR_LENGTH(c.description) > 0  
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.description = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE CHAR_LENGTH(c.description) >= 20  
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.description = 'normal'"
        );
    }


    public function checkCategoriesSeoDescription() {

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc 
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE TRIM(c.seo_description) = '' OR c.seo_description IS NULL  
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_description = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc 
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE CHAR_LENGTH(c.seo_description) < 20 AND CHAR_LENGTH(c.seo_description) > 0
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_description = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc 
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE CHAR_LENGTH(c.seo_description) >= 20 AND CHAR_LENGTH(c.seo_description) <= 200
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_description = 'normal'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc 
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			WHERE CHAR_LENGTH(c.seo_description) >= 200 
			GROUP BY c.id ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_description = 'overflow'"
        );
    }

    public function checkCategoriesSeoKeywords() {
        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc  JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_keywords ck ON ck.category_id = c.id
			LEFT OUTER JOIN keywords k ON ck.keyword_id = k.id
			GROUP BY c.id HAVING GROUP_CONCAT(k.keyword) IS NULL OR CHAR_LENGTH(GROUP_CONCAT(k.keyword)) = 0
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_keywords = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc  JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_keywords ck ON ck.category_id = c.id
			LEFT OUTER JOIN keywords k ON ck.keyword_id = k.id
			GROUP BY c.id HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 200 
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_keywords = 'overflow'
		"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc  JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_keywords ck ON ck.category_id = c.id
			LEFT OUTER JOIN keywords k ON ck.keyword_id = k.id
			GROUP BY c.id 
			HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) < 20 AND CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 0 
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_keywords = 'weak'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc  JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_keywords ck ON ck.category_id = c.id
			LEFT OUTER JOIN keywords k ON ck.keyword_id = k.id
			GROUP BY c.id 
			HAVING CHAR_LENGTH(GROUP_CONCAT(k.keyword)) > 20 AND CHAR_LENGTH(GROUP_CONCAT(k.keyword)) <= 200  
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.seo_keywords = 'normal'"
        );
    }

    public function checkCategoriesImages() {
        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_images im ON im.category_id = c.id
			GROUP BY c.id HAVING COUNT(im.category_id) = 0
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.image = 'none'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_categories c ON qc.category_id = c.id
			LEFT OUTER JOIN catalog_category_images im ON im.category_id = c.id
			GROUP BY c.id HAVING COUNT(im.category_id) > 0
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.image = 'normal'"
        );
    }

    public function checkCategoriesFeatures() {
        $this->db->rawQuery(
            "
		 UPDATE qcc_categories qc SET qc.features = 'normal'"
        );

        $this->db->rawQuery(
            "
		UPDATE qcc_categories qc JOIN (
			SELECT DISTINCT c.id FROM qcc_categories qc
			LEFT JOIN catalog_category_features cf ON cf.category_id = qc.id
			LEFT JOIN catalog_categories c ON c.id = qc.id
			WHERE (c.order_right - c.order_left) = 1
			GROUP BY c.id 
			HAVING COUNT(DISTINCT cf.id) = 0
			ORDER BY c.id ASC
		) c ON qc.category_id = c.id SET qc.features = 'none'"
        );
    }

    public function checkItems() {
        $this->checkItemsDescription();
        $this->checkItemsSeoDescription();
        $this->checkItemsSeoKeywords();
        $this->checkItemsPrices();
        $this->checkItemsImages();
        $this->checkItemsFeatures();
        $this->checkItemsArticul();
    }

    public function checkCategories() {
        $this->checkCategoriesDescription();
        $this->checkCategoriesSeoDescription();
        $this->checkCategoriesSeoKeywords();
        $this->checkCategoriesFeatures();
        $this->checkCategoriesImages();
    }

    public function check() {
        $this->initData();
        $this->checkItems();
        $this->checkCategories();

        return true;
    }

    public function generateItemsLogs($field, $message_id, $payload = []) {
    }

    public function log($message_id, $payload = []) {
    }
}
