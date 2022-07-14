<?php
namespace odissey;

echo "[x] Started at " . date("Y-m-d | H:i:s");

set_time_limit(0);
error_reporting(E_ALL);

$engine_root = realpath(dirname(__FILE__) . '/../..');
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../app/classes/database.php';
require __DIR__ . '/../../app/classes/application.php';
require __DIR__ . '/../../app/classes/account.php';
require __DIR__ . '/../../app/classes/controller.php';
require __DIR__ . '/../../app/classes/site.php';
require __DIR__ . '/../../app/classes/helpers.php';
require __DIR__ . '/../../app/controllers/accountController.php';
require __DIR__ . '/../../app/controllers/catalogController.php';
require __DIR__ . '/../../app/controllers/contentController.php';

$catalog = new CatalogController();
$articles = new ContentController();

$host = 'odisey.ru';
$scheme = 'https://';
$urls = [];

$cleanUp = [
	'%2F' => '/',
	'%3A' => ':',
	'%3F' => '?',
	'%3D' => '=',
	'%26' => '&',
	'%27' => "'",
	'%22' => '"',
	'%3E' => '>',
	'%3C' => '<',
	'%23' => '#',
	'&' => '&'
];

$catalogItems = $catalog->getItemsSEO();
echo "\n[x] Items " . count($catalogItems);
foreach ($catalogItems as $item) {
	$urls[] = $scheme . $host . '/catalog/' . Helpers::getItemSlug($item['id'], $item['title']);
}

$catalogCategories = $catalog->getCategoriesSEO();
echo "\n[x] Categories " . count($catalogCategories);
foreach ($catalogCategories as $item) {
	$urls[] = $scheme . $host . '/catalog/' . Helpers::getCategorySlug($item['id'], $item['title']);
}

$catalogArticles = $articles->getFeedSEO();
echo "\n[x] Feed " . count($catalogArticles);
foreach ($catalogArticles as $item) {
	$urls[] = $scheme . $host . '/feed/' . Helpers::getArticleSlug($item['id'], $item['title']);
}

$sitemapXML = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">
<!-- Last update of sitemap ' . date("Y-m-d H:i:s+03:00") . ' -->';
$sitemapTXT = null;
foreach ($urls as $k) {
	$sitemapXML .= "\r<url><loc>{$k}</loc><changefreq>weekly</changefreq><priority>0.5</priority></url>";
}
$sitemapXML .= "\r</urlset>";
$sitemapXML = trim(strtr($sitemapXML, $cleanUp));
$fp = fopen($engine_root . DIRECTORY_SEPARATOR . 'sitemap.xml', 'w+');
if (!fwrite($fp, $sitemapXML)) {
	echo 'Ошибка записи!';
}
fclose($fp);

echo "\n[x] XML file generated \n";

Helpers::gzCompressFile($engine_root . DIRECTORY_SEPARATOR . 'sitemap.xml');

echo "\n[x] Done\n";
