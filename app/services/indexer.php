<?php
namespace odissey;

echo "[x] Started at " . date("Y-m-d | H:i:s");

set_time_limit(0);
error_reporting(E_ALL);

// Функция для сбора ссылок
include_once __DIR__ . '/../../app/config.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Support/TokenizerInterface.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Support/Tokenizer.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Support/Collection.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Indexer/TNTIndexer.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Stemmer/Stemmer.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Stemmer/PorterStemmer.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/FileReaders/FileReaderInterface.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/FileReaders/TextFileReader.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Connectors/ConnectorInterface.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Connectors/Connector.php';
include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/Connectors/MySqlConnector.php';

include_once __DIR__ . '/../../vendor/teamtnt/tntsearch/src/TNTSearch.php';

include_once __DIR__ . '/../../vendor/wamania/php-stemmer/src/Stemmer.php';
include_once __DIR__ . '/../../vendor/wamania/php-stemmer/src/Utf8.php';
include_once __DIR__ . '/../../vendor/wamania/php-stemmer/src/Stem.php';
include_once __DIR__ . '/../../vendor/wamania/php-stemmer/src/Russian.php';

use TeamTNT\TNTSearch\TNTSearch;
use Wamania\Snowball\Russian;

$tntconfig = [
	'driver' => 'mysql',
	'host' => Configuration::DATABASE_HOST,
	'database' => Configuration::DATABASE_DB,
	'username' => Configuration::DATABASE_USER,
	'password' => Configuration::DATABASE_PASS,
	'storage' => Configuration::SEARCH_CACHE,
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci'
];

$tnt = new TNTSearch;
$tnt->loadConfig($tntconfig);

//$categoryQ = "SELECT c.id, c.title, GROUP_CONCAT(k.keyword SEPARATOR ',') AS catalog_keywords
//	FROM catalog_categories c
//	LEFT OUTER JOIN catalog_category_keywords kc ON (kc.category_id = c.id)
//	LEFT OUTER JOIN keywords k ON (k.id = kc.keyword_id)
//	WHERE c.flag_active = 1 GROUP BY c.id;";

$queries = [
    'category.user' => "SELECT id, title FROM catalog_categories WHERE flag_active = 1;",
    'category.admin' => "SELECT id, title FROM catalog_categories",
    'items.admin' => "SELECT i.id, i.title, i.articul FROM catalog_items i;",
    'items.user' => "SELECT i.id, i.title, i.articul FROM catalog_items i WHERE i.flag_active = 1;"
//    'items-articul.user.nostem' => "SELECT i.id, i.articul FROM catalog_items i WHERE i.flag_active = 1;",
//    'items-articul.admin.nostem' => "SELECT i.id, i.articul FROM catalog_items i;"

//  'items.user' => "
//    SELECT i.id, i.title, i.articul, TRIM(GROUP_CONCAT(k.keyword SEPARATOR ',')) AS keywords
//    FROM catalog_items i
//    LEFT OUTER JOIN catalog_items_keywords ki ON (ki.item_id = i.id)
//    LEFT OUTER JOIN keywords k ON (k.id = ki.keyword_id)
//    WHERE i.flag_active = 1
//    GROUP BY i.id;",
];

foreach ($queries as $k => $q) {
    $indexer = $tnt->createIndex($k);
    $indexer->disableOutput = true;
    $indexer->setPrimaryKey('id');
    if (strpos($k, 'nostem') !== false) {
        $indexer->setStemmer(new Russian);
    }
    $indexer->query($q);
    $indexer->run();
}