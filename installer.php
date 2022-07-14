<?php
passthru("npm install -q -s");
$path = [
    [
        "source" => "./node_modules/raty-js/lib",
        "dest" => "./vendor/raty-js",
    ],
    [
        'source' => "./node_modules/lightbox2/dist",
        "dest" => "./vendor/lightbox2",
    ],
    [
        'source' => "./node_modules/ion-rangeslider",
        "dest" => "./vendor/ion-rangeslider",
    ],
    [
        'source' => "./node_modules/jquery-treetable",
        "dest" => "./vendor/jquery-treetable",
    ],
    [
        'source' => "./node_modules/inputmask/dist",
        "dest" => "./vendor/inputmask",
    ],

];

foreach ($path as $p) {
    if (!is_dir($p['dest'])) {
        mkdir($p['dest'], 0755);
    }
    if (is_dir($p['source'])) {
        foreach ($iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($p['source'], \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            $pd = $p['dest'].DIRECTORY_SEPARATOR.$iterator->getSubPathName();
            if ($item->isDir()) {
                if (!is_dir($pd)) {
                    mkdir($pd);
                }
            } else {
                copy($item, $pd);
            }
        }
    }
}
