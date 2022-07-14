<?php
namespace odissey;

echo "[x] Started at " . date("Y-m-d | H:i:s");

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

$catalog = new CatalogController();

$catalog->calculateItemsCounts();
echo "\n[x] Done\n";
