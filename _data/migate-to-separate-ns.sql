DROP TABLE IF EXISTS `catalog_ns`;

CREATE TABLE `catalog_ns` (
	`order_left` INT(10) UNSIGNED NULL DEFAULT NULL,
	`order_right` INT(10) UNSIGNED NULL DEFAULT NULL,
	`category_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`order_sort` INT(3) UNSIGNED NULL DEFAULT NULL,
	`order_level` INT(2) UNSIGNED NULL DEFAULT NULL,
   KEY `id,lft,rgt` (`category_id`,`order_left`,`order_right`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


INSERT INTO catalog_ns (order_left, order_right, order_level, order_sort, category_id)
  SELECT cc.order_left, cc.order_right, cc.order_level, cc.order_id, cc.id
  FROM catalog_categories cc;
  

ALTER TABLE catalog_categories
DROP COLUMN order_left, 
DROP COLUMN order_right, 
DROP COLUMN order_level, 
DROP COLUMN order_id;
