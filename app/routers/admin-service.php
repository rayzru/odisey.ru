<?php

namespace odissey;

$catalog = new CatalogController();
$qcc = new QccController();

$this->respond(
    'GET',
    '/service/?',
    function ($request, $response, $service, $app) {
        $app->site->template = 'admin/admin-service';
        /**
         * @var \Smarty
         */
        $app->tpl->display('layouts/admin-default.tpl');
    }
);

$this->respond(
    'POST',
    '/service/qcc/items',
    function (\Klein\Request $request, \Klein\Response $response, $service, $app) use ($catalog) {
        $pid = $request->param('pid');
        $response->json($catalog->getQccItems($pid));
    }
);

$this->respond(
    'GET',
    '/service/catalog/rebuild/?',
    function ($request, $response) use ($catalog) {
        $catalog->repairTree();

        return $response->json(['success' => true]);
    }
);

$this->respond(
    'GET',
    '/service/catalog/cleanup/?',
    function ($request, $response) use ($catalog) {
        $catalog->cleanUpCategoryTree();
        return $response->json(['success' => true]);
    }
);


$this->respond(
    'GET',
    '/service/catalog/recalcItems/?',
    function ($request, $response) use ($catalog) {
        $catalog->calculateItemsCounts();
        $catalog->cleanUpCategoryTree();

        return $response->json(['success' => true]);
    }
);

$this->respond(
    'GET',
    '/service/[items|categories:operation]/?',
    function (\Klein\Request $request, $response, $service, $app) use ($catalog, $qcc) {


        $app->site->addScript("/vendor/jquery-treetable/jquery.treetable.js");
        $app->site->addScript("/vendor/jquery-treetable/jquery.treetable-ajax-persist.js");
        $app->site->addScript("/vendor/jquery-treetable/persist-min.js");
        $app->site->addStyle('/vendor/jquery-treetable/css/jquery.treetable.css');

        $app->site->addScript('/assets/js/admin-qcc.js');

        $action = $request->param('operation');
        $app->tpl->assign('action', $action);
        $app->tpl->assign('qccValues', $catalog->qccValues);

        switch ($action) {
            case 'categories':
                $qcc->check();
                $app->tpl->assign('qccKeys', $catalog->qccCategoryKeys);
                $app->tpl->assign('tree', $catalog->getQccCategoriesTree());
                break;
            case 'items':
                $qcc->check();
                $app->tpl->assign('qccKeys', $catalog->qccKeys);
                $app->tpl->assign('tree', $catalog->getQccItemsTree());
                break;
        }

        $app->site->title = "Обслуживание";
        $app->site->template = 'admin/admin-service';
        $app->tpl->display('layouts/admin-default.tpl');
    }
);


$this->respond(
    'GET',
    '/service/getReport/[items|categories:operation]/?',
    function (\Klein\Request $request, $response, $service, $app) use ($catalog, $qcc) {

        set_time_limit(120);
        ini_set('memory_limit', '256M');

        define('START_CHAR', 69); // F

        $stocks = [
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

        $qcc->check();

        $operation = $request->operation;

        $title = ($operation == 'items') ? 'Товары' : 'Разделы';

        $date = date('d-m-Y-H-i');
        $title = $title.' - '.$date;
        $filename = "report-{$operation}-{$date}.xlsx";
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Генератор отчетов Odisey.ru");
        $objPHPExcel->getProperties()->setLastModifiedBy("Генератор отчетов Odisey.ru");
        $objPHPExcel->getProperties()->setTitle("Отчет по наполненности");
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription("");
        $objPHPExcel->setActiveSheetIndex(0);

        $activeSheet = $objPHPExcel->getActiveSheet();
        $activeSheet->setTitle('Отчет');

        $qccKeys = [
            'qcc_seo_description' => 'СЕО описание',
            'qcc_keywords' => 'Ключевые слова',
            'qcc_description' => 'Описание',
            'qcc_features' => 'Характеристики',
            'qcc_images' => 'Изображения',
        ];

        $activeSheet
            ->SetCellValue('A1', 'Артикул')
            ->SetCellValue('B1', 'Наименование')
            ->SetCellValue('C1', 'Цена')
            ->SetCellValue('D1', 'Прейскурант')
            ->SetCellValue('E1', 'Активность')
            ->SetCellValue('F1', 'Характеристики');

        $q = START_CHAR;
        foreach ($qccKeys as $key => $qccKey) {
            $q++;
            $activeSheet->SetCellValue(chr($q).'2', $qccKey);
        }

        $activeSheet->mergeCells("F1:J1");

        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);

        $shift = 3;
        $itemsCounter = 0;

        $styles = [
            1 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THICK,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            2 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            3 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            4 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_HAIR,
                    ],
                ],
            ],
            5 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_NONE,
                    ],
                ],
            ],
            6 => [
                'borders' => [
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_NONE,
                    ],
                ],
            ],
            'x' => [
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFF79494'],
                ],
            ],
            'y' => [
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFC5F19A'],
                ],
            ],
        ];

        $i = 0;
        if ($operation === 'items') {
            $items = $catalog->getQccItemsTree();
            foreach ($items as $index => $item) {
                // ss
                $i = $index + $shift + $itemsCounter;
                $activeSheet
                    ->setShowSummaryBelow(false)
                    ->getRowDimension($i)
                    ->setRowHeight(30)
                    ->setOutlineLevel($item['lvl']);

                // $activeSheet->mergeCells("A{$i}:B{$i}");
                $activeSheet->getStyle("A{$i}:J{$i}")->applyFromArray($styles[$item['lvl']]);
                $activeSheet->SetCellValue('B'.$i, $item['title']);

                $k = START_CHAR;
                foreach ($qccKeys as $key => $qccKey) {
                    $k++;
                    $v = $item['qcc'][$key]['total'] - $item['qcc'][$key]['normal'];
                    $activeSheet->SetCellValue(chr($k).$i, $v);
                }

                // $activeSheet->SetCellValue(chr(++$k) . $i, $item['qcc'][$key]['total']);

                $activeSheet
                    ->getStyleByColumnAndRow(1, $i)
                    ->getFont()
                    ->setBold(true);

                if (count($item['items']) && $item['is_leaf'] === true) {
                    foreach ($item['items'] as $j => $ci) {
                        $k = $i + $j + 1;
                        $activeSheet
                            ->getRowDimension($k)
                            ->setOutlineLevel($item['lvl'] + 1);
                        $activeSheet->SetCellValue('A'.$k, $ci['articul']);
                        $activeSheet->SetCellValue('B'.$k, $ci['title']);
                        $activeSheet->SetCellValue('C'.$k, $ci['price']);
                        $activeSheet->SetCellValue('D'.$k, $stocks[$ci['stock']]['title']);
                        $activeSheet->SetCellValue('E'.$k, $ci['flag_active'] ? '' : 'X');
                        $activeSheet->getStyle('E'.$k)
                            ->applyFromArray(($ci['flag_active'] ? $styles['y'] : $styles['x']));

                        $q = START_CHAR;
                        foreach ($qccKeys as $key => $qccKey) {
                            $q++;
                            $activeSheet->SetCellValue(chr($q).$k, $ci[$key] === 'normal' ? '' : 'X');
                            $activeSheet->getStyle(chr($q).$k)
                                ->applyFromArray(($ci[$key] === 'normal' ? $styles['y'] : $styles['x']));
                        }
                    }
                    $itemsCounter += count($item['items']);
                }
            }
        } elseif ($operation === 'categories') {
            // $items = $catalog->getQccCategoriesTree();
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }
);

$this->respond(
    'GET',
    '/service/qcc/check/?',
    function ($request, $response) use ($qcc) {
        $response->json(
            [
                'status' => $qcc->check(),
            ]
        );
    }
);
