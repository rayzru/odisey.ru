<?php
namespace odissey;

echo "[x] Started at " . date("Y-m-d | H:i:s");

use Smarty;
use ZipArchive;

set_time_limit(0);
error_reporting(E_ALL);

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
require __DIR__ . '/../../app/controllers/ordersController.php';
require __DIR__ . '/../../app/controllers/contentController.php';

$engine_root = realpath(dirname(__FILE__) . '/../..');
$file = $engine_root . DIRECTORY_SEPARATOR . 'yml-catalog.xml';

$tpl = new Smarty();
$catalog = new CatalogController();

$tpl->template_dir = Configuration::TEMPLATE_DIR;
$tpl->compile_dir = Configuration::TEMPLATE_CACHE;
$tpl->cache_dir = Configuration::TEMPLATE_CACHE;
$tpl->compile_id = 'service';
$tpl->caching = 1;
$tpl->debugging = Configuration::DEBUG;
$tpl->plugins_dir[] = implode(DIRECTORY_SEPARATOR, ['app', 'addons', 'smarty-plugins']);

$tpl->assign('i', $catalog->getItems(['active' => 1]));
$tpl->assign('c', $catalog->getCategories());

if (file_exists($file)) {
	unlink($file);
}

$fp = fopen($file, 'w+');
if (!fwrite($fp, $tpl->fetch('scripts' . DIRECTORY_SEPARATOR . 'yml-catalog.tpl'))) {
	echo 'Ошибка записи!';
}
fclose($fp);

try {
	$zip = new ZipArchive;
	$zipFile = $engine_root . DIRECTORY_SEPARATOR . 'yml-catalog.xml.zip';

	if (file_exists($zipFile)) {
		unlink($zipFile);
	}

	if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
		$zip->addFile($file, 'yml-catalog.xml');
		$zip->close();
	}

	Helpers::gzCompressFile($engine_root . DIRECTORY_SEPARATOR . 'yml-catalog.xml');

	echo "[x] Done";
} catch (\Exception $exception) {
	echo "[x] Error";
}
