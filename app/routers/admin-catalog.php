<?php

namespace odissey;

use Intervention\Image\ImageManagerStatic as Image;
use Klein\Request;
use Klein\Response;

$catalog = new CatalogController();
$content = new ContentController();
$keywords = new KeywordsController();
$qcc = new QccController();

$this->respond(
    'GET',
    '/catalog/?',
    function ($request, $response, $service, $app) {
        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.1.1/typeahead.bundle.min.js");
        $app->site->addScript("https://code.jquery.com/ui/1.12.1/jquery-ui.min.js");
        $app->site->addScript("/vendor/mar10/fancytree/dist/jquery.fancytree-all.min.js");
        $app->site->addStyle('/vendor/mar10/fancytree/dist/skin-win8-n/ui.fancytree.min.css');
        $app->site->addScript('/assets/js/admin-search.js');
        $breadcrumbs[] = ['title' => 'Каталог', 'url' => '/admin/catalog'];
        $app->site->template = 'admin/admin-catalog-index';
        /**
         * @var \Smarty
         */
        $app->tpl->display('layouts/admin-catalog.tpl');
    }
);

$this->respond(
    'POST',
    '/catalog/services/qcc/items',
    function (Request $request, Response $response, $service, $app) use ($catalog) {
        $pid = $request->param('pid');
        $response->json($catalog->getQccItems($pid));
    }
);

$this->respond(
    ['GET', 'POST'],
    '/catalog/[i:id]/[items|category|addcategory|additem:operation]?',
    function (Request $request, Response $response, $service, $app) use ($catalog) {
        if ($request->method('POST') && ($request->operation === 'category' || empty($request->operation))) {
            $factory = new Validation();

            $rules = ['title' => ['required', 'max:255']];

            $messages = [
                'title.required' => 'Обязательное поле',
                'title.max' => 'Максимальная длина 255 символов',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                $categoryData = $request->params();
            } else {
                $cdata = $request->params();
                $oldCategory = $catalog->getCategory($request->id);
                $catalog->updateCategory($request->id, $cdata);
                if ($oldCategory['pid'] !== (int)$cdata['pid']) {
                    $catalog->repairTree();
                    $catalog->calculateItemsCounts();
                }

                if ($catalog->isLeafCategory($request->id)) {
                    $catalog->removeCategoryFeatures($request->id);
                    if (isset($request->features)) {
                        $catalog->addCategoryFeatures(
                            $request->id,
                            $request->features
                        );
                    }
                }

                $catalog->removeCategoryKeywords($request->id);
                if (isset($request->keywords)) {
                    $catalog->addCategoryKeywords(
                        $request->id,
                        $request->keywords
                    );
                }
            }
        }

        if ($request->method('POST') && $request->operation === 'additem') {
            // save item

            $factory = new Validation();

            $rules = [
                'title' => ['required', 'max:255'],
                'articul' => ['required', 'max:15', 'alpha_num'],
                'price' => ['required', 'between:0,999999.99'],
            ];

            $messages = [
                'title.required' => 'Обязательное поле',
                'title.max' => 'Максимальная длина 255 символов',
                'articul.required' => 'Обязательное поле',
                'articul.max' => 'Максимальная длина 15 символов',
                'articul.alpha_num' => 'Артикул может состоять из символов латинского алфавита и цифр',
                'price.required' => 'Стоимость позиции должна быть указана',
                'price.between' => 'В качестве стоимости позиции укажите любое положительное число или 0',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            $item = $request->params();

            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                unset($item['id']);
                $app->tpl->assign('item', $item);
            } else {
                $item_id = $catalog->addItem($item);
                $catalog->updatePrices([$item_id => $request->price]);
                $catalog->setItemCategory($item_id, $request->category_id);

                $catalog->removeItemKeywords($request->id);
                if (isset($request->keywords)) {
                    $catalog->addItemsKeywords(
                        $request->id,
                        $request->keywords
                    );
                }

                if (isset($request->exit)) {
                    $response->redirect(
                        ('/admin/catalog/'.$request->category_id)
                    )->send();
                } else {
                    $response->redirect(('/admin/catalog/p'.$item_id))->send();
                }
            }
        }

        $app->site->addScript("https://code.jquery.com/ui/1.12.1/jquery-ui.min.js");
        $app->site->addScript("/vendor/mar10/fancytree/dist/jquery.fancytree-all.min.js");
        $app->site->addStyle('/vendor/mar10/fancytree/dist/skin-win8-n/ui.fancytree.min.css');

        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/vendor/jquery.ui.widget.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js');
        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.1.1/typeahead.bundle.min.js");
        $app->site->addScript('/assets/js/admin-search.js');

        if ($catalog->isLeafCategory($request->id) && ($request->operation === 'items' || empty($request->operation))) {
            $app->tpl->assign('items', $catalog->getItems(['category' => $request->id]));
            $app->site->addScript('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
            $app->site->addStyle('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
            // $app->site->addStyle('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.theme.min.css');
            $app->site->addScript('/assets/js/admin-items.js');
        }

        if ($request->operation === 'additem') {
            $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
            $app->site->addScript("/assets/js/tinymce.init.js");
            $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
            $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
            $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
            $app->site->addScript("/assets/js/admin-item-form.js");
        }


        if (($request->operation === 'category' || empty($request->operation))) {
            $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
            $app->site->addScript("/assets/js/tinymce.init.js");
            $app->site->addScript("/assets/js/admin-category-form.js");
            $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
            $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
            $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
            $app->tpl->assign('keywords', $catalog->getCategoryKeywords($request->id));

            if ($catalog->isLeafCategory($request->id)) {
                $app->tpl->assign('features', $catalog->getCategoryFeatures($request->id));
            }

        }

        $breadcrumbs[] = ['title' => 'Каталог', 'url' => '/admin/catalog'];

        $path = $catalog->getPath($request->id);

        foreach ($path as $p) {
            if ($p['id'] != $request->id) {
                $breadcrumbs[] = [
                    'title' => $p['title'],
                    'url' => '/admin/catalog/'.$p['id'],
                ];
            }
        }

        $cat = isset($categoryData) ? $categoryData : $catalog->getCategory($request->id);

        $app->tpl->assign('breadcrumbs', $breadcrumbs);
        $app->tpl->assign('path', $path);
        $app->tpl->assign('category', $cat);
        $app->tpl->assign('pcategory', $catalog->getCategory($cat['pid']));
        $app->tpl->assign('action', $request->operation);
        $app->tpl->assign('stocks', $catalog->getStockStrings());

        $app->site->template = 'admin/admin-catalog';
        $app->tpl->display('layouts/admin-catalog.tpl');
    }
);

$this->respond(
    'DELETE',
    '/catalog/[i:id]/?',
    function (Request $request) use ($catalog) {
        // WARNING! This removes all nested data with catalog items
        // $target = $catalog->getCategory($request->id);

        return json_encode($catalog->removeCategory($request->id));
        // $response->redirect(('/admin/catalog/' . $target['pid']))->send();
    }
);

$this->respond(
    'POST',
    '/catalog/create-category/?',
    function (Request $request, Response $response, $service, $app) use ($catalog) {
        $title = $request->title ? $request->title : 'Новый раздел';
        $parent = $request->parent ? $request->parent : 0;
        $newCategoryId = $catalog->addCategory($title, $parent);
        $redirect = $newCategoryId ? $newCategoryId : $parent;
        $response->redirect(('/admin/catalog/'.$redirect))->send();
    }
);

$this->respond(
    ['GET', 'POST'],
    '/catalog/p[i:id]/?',
    function (Request $request, Response $response, $service, $app) use ($catalog) {
        if ($request->method('POST')) {
            $oldItem = $catalog->getItem($request->id);

            $factory = new Validation();

            $rules = [
                'title' => ['required', 'max:255'],
                'articul' => ['required', 'max:15', 'alpha_num'],
                'price' => ['required', 'between:0,999999.99'],
            ];

            $messages = [
                'title.required' => 'Обязательное поле',
                'title.max' => 'Максимальная длина 255 символов',
                'articul.required' => 'Обязательное поле',
                'articul.max' => 'Максимальная длина 15 символов',
                'articul.alpha_num' => 'Артикул может состоять из символов латинского алфавита и цифр',
                'price.required' => 'Стоимость позиции должна быть указана',
                'price.between' => 'В качестве стоимости позиции укажите любое положительное число или 0',
            ];

            $validator = $factory->make($request->params(), $rules, $messages);

            if ($validator->fails()) {
                $app->tpl->assign('errors', $validator->messages()->messages());
                $app->tpl->assign('item', $request->params());
            } else {
                $catalog->updateItemFeatures($request->id, $request->feature);

                // insert new price if not thae same
                if ((float)$oldItem['price'] != (float)$request->price) {
                    $catalog->updatePrices([$request->id => $request->price]);
                }

                if ($oldItem['category_id'] != $request->category_id && (int)$request->category_id > 0) {
                    $catalog->setItemCategory(
                        $request->id,
                        $request->category_id
                    );
                }

                // after all get fresh data
                $catalog->updateItem($request->id, $request->params());

                // update keywords
                $catalog->removeItemKeywords($request->id);
                if (isset($request->keywords)) {
                    $catalog->addItemsKeywords(
                        $request->id,
                        $request->keywords
                    );
                }

                if (isset($request->exit)) {
                    $response->redirect(
                        ('/admin/catalog/'.$request->category_id)
                    )->send();
                } else {
                    $response->redirect(('/admin/catalog/p'.$request->id))
                        ->send();
                }
            }
        }
        $item = $catalog->getItem($request->id);

        $app->site->addScript("https://code.jquery.com/ui/1.12.1/jquery-ui.min.js");
        $app->site->addScript("/vendor/mar10/fancytree/dist/jquery.fancytree-all.min.js");
        $app->site->addStyle('/vendor/mar10/fancytree/dist/skin-win8-n/ui.fancytree.min.css');

        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.1.1/typeahead.bundle.min.js");
        $app->site->addScript('/assets/js/admin.js');
        $app->site->addScript('/assets/js/admin-search.js');
        $app->site->addScript("/vendor/tinymce/tinymce/tinymce.min.js ");
        $app->site->addScript("/assets/js/tinymce.init.js");
        $app->site->addScript("/assets/js/admin-item-form.js");
        $app->site->addScript('/vendor/select2/select2/dist/js/select2.full.min.js');
        $app->site->addScript('/vendor/select2/select2/dist/js/i18n/ru.js');
        $app->site->addStyle('/vendor/select2/select2/dist/css/select2.min.css');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/vendor/jquery.ui.widget.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js');

        $breadcrumbs = [];
        $breadcrumbs[] = ['title' => 'Каталог', 'url' => '/admin/catalog'];

        $path = $catalog->getPath($item['category_id']);

        foreach ($path as $p) {
            if ($p['id'] != $request->id) {
                $breadcrumbs[] = [
                    'title' => $p['title'],
                    'url' => '/admin/catalog/'.$p['id'],
                ];
            }
        }

        $app->tpl->assign(
            'category',
            $catalog->getCategory($item['category_id'])
        );
        $app->tpl->assign('breadcrumbs', $breadcrumbs);
        $app->tpl->assign('path', $path);
        $app->tpl->assign('item', $item);
        $app->tpl->assign(
            'cloneItems',
            $catalog->getItems(
                ['category' => $item['category_id']]
            )
        );

        $app->tpl->assign('keywords', $catalog->getItemKeywords($request->id));
        $app->tpl->assign('features', $catalog->getItemFeatures($request->id));
        $app->tpl->assign('articles', $catalog->getItemArticles($request->id));

        $app->site->template = 'admin/admin-catalog-item';
        $app->tpl->display('layouts/admin-catalog.tpl');
    }
);

$this->respond(
    'GET',
    '/catalog/cleanup/?',
    function (Reuest $request, Response $response) use ($catalog) {
        $catalog->cleanUpCategoryTree();
        return $response->json(['success' => true]);
    }
);

$this->respond(
    'GET',
    '/catalog/getTree/?',
    function ($request, Response $response) use ($catalog) {
        $key = (isset($_GET['key'])) ? $_GET['key'] : 0;
        $categories = $catalog->getCategoriesTree($key, false);
        $response->json($categories);
    }
);

$this->respond(
    'POST',
    '/catalog/sortTree/?',
    function ($request, Response $response) use ($catalog) {
        $sortedArray = (isset($_POST['data'])) ? $_POST['data'] : [];
        $response->json(['success' => $catalog->sortCategoriesTree($sortedArray)]);
    }
);


$this->respond(
    'GET',
    '/catalog/getKeywords/?',
    function ($request, Response $response) use ($keywords) {
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';
        $response->json($keywords->query($q));
    }
);

$this->respond(
    'GET',
    '/catalog/getFeatures/?',
    function ($request, Response $response) use ($catalog) {
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';
        $response->json($catalog->getFeatures(['query' => $q]));
    }
);

$this->respond(
    'GET',
    '/catalog/getArticles/?',
    function ($request, \Klein\Response $response) use ($catalog, $content) {
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';
        $response->json(
            $content->getList(
                ['query' => $q, 'type' => 'article', 'active' => true]
            )
        );
    }
);

$this->respond(
    'GET',
    '/catalog/getLeafCategories/?',
    function ($request, \Klein\Response $response) use ($catalog) {
        $res = [];
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';
        $categoriesList = $catalog->getLeafCategoriesList(0, $q);
        if (!empty($categoriesList)) {
            $categories = $catalog->getCategories(['id' => $categoriesList]);
            $res = array_map(
                function ($c) {
                    return [
                        'id' => $c['id'],
                        'text' => $c['title'],
                    ];
                },
                $categories
            );
        }
        $response->json(['results' => $res]);
    }
);

$this->respond(
    'GET',
    '/catalog/pickParentCategory/[i:id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $res = [];
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';

        $categoriesList = $catalog->pickParentCategoriesList($request->id, $q);

        if (!empty($categoriesList)) {
            $categories = $catalog->getCategories(['id' => $categoriesList]);
            $res = array_map(
                function ($c) {
                    return [
                        'id' => $c['id'],
                        'text' => $c['title'],
                    ];
                },
                $categories
            );
        }

        // Add ROOT element
        array_unshift($res, ['id' => 0, 'text' => '(Корневой раздел)']);

        $response->json(['results' => $res]);
    }
);


$this->respond(
    'GET',
    '/catalog/getCategories/?',
    function ($request, \Klein\Response $response) use ($catalog) {
        $q = (isset($_GET['term'])) ? $_GET['term'] : '';
        $res = array_map(
            function ($el) {
                return [
                    'id' => $el['id'],
                    'text' => $el['title'],
                ];
            },
            $catalog->getCategories(['query' => $q])
        );

        return $response->json(['results' => $res]);
    }
);

$this->respond(
    'POST',
    '/catalog/category/[i:id]/uploadImage/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {

        $upload_dir = '/assets/images/catalog/';
        $img = Image::make($_FILES['filename']['tmp_name']);
        $ext = 'jpg';

        switch ($img->mime()) {
            case "image/jpeg":
                $ext = 'jpg';
                break;
            case "image/gif":
                $ext = 'gif';
                break;
            case "image/png":
                $ext = 'png';
                break;
        }

        $filename = sha1(time()).".".$ext;
        $dest = $_SERVER['DOCUMENT_ROOT'].$upload_dir.$filename[0].'/'.$filename[1].'/';
        Helpers::mkpath($dest);

        $img->resize(
            1200,
            1200,
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        )->save($dest.$filename);

        $status = $catalog->setCategoryImage($request->id, $filename) ? 'success' : 'error';
        $response->json(['status' => $status, 'filename' => Helpers::getMediaCachePath($filename, '200x200'),]);
    }
);

$this->respond(
    'POST',
    '/catalog/item/[i:id]/uploadImage/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $upload_dir = '/assets/images/catalog/';
        $img = Image::make($_FILES['filename']['tmp_name']);
        $ext = 'jpg';
        switch ($img->mime()) {
            case "image/jpeg":
                $ext = 'jpg';
                break;
            case "image/gif":
                $ext = 'gif';
                break;
            case "image/png":
                $ext = 'png';
                break;
        }

        $filename = sha1(time()).".".$ext;
        $dest = $_SERVER['DOCUMENT_ROOT'].$upload_dir.$filename[0].'/'.$filename[1].'/';
        Helpers::mkpath($dest);

        $img->resize(
            1200,
            1200,
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        )->save($dest.$filename);

        $status = $catalog->addItemImage($request->id, $filename) ? 'success' : 'error';
        $response->json(['status' => $status, 'filename' => Helpers::getMediaCachePath($filename, '200x200')]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/category/[i:id]/deleteImage/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->removeCategoryImage($request->id)]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/deleteImage/[i:image]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->deleteItemImage($request->image)]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/defaultImage/[i:image]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->setDefaultItemImage($request->id, $request->image)]);
    }
);

$this->respond(
    'GET',
    '/catalog/item/[i:id]/getImages/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['images' => $catalog->getItemImages($request->id),]);
    }
);

$this->respond(
    'GET',
    '/catalog/item/[i:id]/linkArticle/[i:article_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['success' => $catalog->linkItemArticle($request->id, $request->article_id)]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/unlinkArticle/[i:article_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['success' => $catalog->unlinkItemArticle($request->id, $request->article_id)]);
    }
);

$this->respond(
    'POST',
    '/catalog/item/[i:id]/video/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $data = $catalog->addItemVideo($request->id, $request->url);
        $response->json($data ? $data : ['status' => false]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/video/[i:video_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->deleteItemVideo($request->video_id)]);
    }
);

$this->respond(
    'GET',
    '/catalog/item/[i:id]/related/[i:related_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->addItemRelated($request->id, $request->related_id)]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/related/[i:related_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->removeItemRelated($request->id, $request->related_id)]);
    }
);

$this->respond(
    'GET',
    '/catalog/item/[i:id]/similar/[i:similar_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->addItemSimilar($request->id, $request->similar_id)]);
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/similar/[i:similar_id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->removeItemSimilar($request->id, $request->similar_id),]);
    }
);

$this->respond(
    'GET',
    '/catalog/items/ajax/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $items = $catalog->pickItem($request->q);
        $response->json($items);
    }
);

$this->respond(
    'POST',
    '/catalog/category/[i:id]/reorder/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->updateItemsOrder($request->items),]);
    }
);

$this->respond(
    'GET',
    '/catalog/item/[i:id]/getFeatures/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json($catalog->getItemFeatures($request->id));
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/deleteFeature/[i:featureId]?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog) {
        $response->json(['status' => $catalog->deleteItemFeature($request->id, $request->featureId),]);
    }
);

$this->respond(
    'POST',
    '/catalog/item/[i:id]/flag/[set|unset:order]/[new|top|commission|special:flag]/?',
    function ($request, $response) use ($catalog) {
        $flagValue = ($request->order === 'set') ? 1 : 0;
        switch ($request->flag) {
            case 'new':
                $result = $catalog->setItemFlagNew($request->id, $flagValue);
                break;
            case 'top':
                $result = $catalog->setItemFlagTop($request->id, $flagValue);
                break;
            case 'commission':
                $result = $catalog->setItemFlagCommission($request->id, $flagValue);
                break;
            case 'special':
                $result = $catalog->setItemFlagSpecial($request->id, $flagValue);
                break;
            default:
                $result = false;
        }

        $response->json(
            [
                'status' => $result,
            ]
        );
    }
);

$this->respond(
    'DELETE',
    '/catalog/item/[i:id]/?',
    function (\Klein\Request $request, \Klein\Response $response) use ($catalog
    ) {
        $response->json(
            [
                'status' => $catalog->deleteItem($request->id),
            ]
        );
    }
);

$this->respond(
    ['GET', 'POST'],
    '/catalog/import/[prices|titles|batch:action]?/[:step]?',
    function ($request, $response, $service, $app) use ($catalog) {
        $app->site->addScript("https://code.jquery.com/ui/1.12.1/jquery-ui.min.js");
        $app->site->addScript("/vendor/mar10/fancytree/dist/jquery.fancytree-all.min.js");
        $app->site->addStyle('/vendor/mar10/fancytree/dist/skin-win8-n/ui.fancytree.min.css');

        $app->site->addScript("https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.1.1/typeahead.bundle.min.js");
        $app->site->addScript('/assets/js/admin-search.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/vendor/jquery.ui.widget.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.iframe-transport.js');
        $app->site->addScript('/vendor/blueimp/jquery-file-upload/js/jquery.fileupload.js');

        $action = $request->action;
        $step = isset($request->step) ? $request->step : 'init';

        $app->tpl->assign('action', $action);
        $app->tpl->assign('step', $step);
        switch ($action) {
            case 'prices':
                switch ($step) {
                    case 'upload':
                        $inputFileName = $_FILES['file']['tmp_name'];
                        $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);
                        $articuls = array_map(
                            function ($el) {
                                return $el[0];
                            },
                            $sheetData
                        );

                        $items = $catalog->getItems(['articul' => $articuls]);

                        $res = array_map(
                            function ($data) use ($sheetData) {
                                $key = array_search(
                                    $data['articul'],
                                    array_column($sheetData, 0),
                                    true
                                );
                                if ($key !== false) {
                                    return [
                                        'id' => $data['id'],
                                        'articul' => $data['articul'],
                                        'title' => $data['title'],
                                        'price' => (float)floatval($sheetData[$key][1]),
                                    ];
                                }
                            },
                            $items
                        );
                        $app->tpl->assign('items', $res);
                        break;
                    case 'process':
                        $prices = $request->price;
                        $catalog->updatePrices($prices);
                        $response->redirect('/admin/catalog/import/prices/success')->send();
                        break;
                }
                break;
            case 'titles':
                switch ($step) {
                    case 'upload':
                        $inputFileName = $_FILES['file']['tmp_name'];
                        $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);
                        $articuls = array_map(
                            function ($el) {
                                return $el[0];
                            },
                            $sheetData
                        );

                        $items = $catalog->getItems(['articul' => $articuls]);

                        $res = array_map(
                            function ($data) use ($sheetData) {
                                $key = array_search(
                                    $data['articul'],
                                    array_column($sheetData, 0),
                                    true
                                );
                                if ($key !== false) {
                                    return [
                                        'id' => $data['id'],
                                        'articul' => $data['articul'],
                                        'title' => $data['title'],
                                        'new_title' => $sheetData[$key][1],
                                    ];
                                }
                            },
                            $items
                        );
                        $app->tpl->assign('items', $res);
                        break;
                    case 'process':
                        $titles = $request->title;
                        $catalog->updateTitles($titles);
                        $response->redirect(
                            '/admin/catalog/import/prices/success'
                        )->send();
                        break;
                }
                break;
            case 'batch':
                switch ($step) {
                    case 'upload':
                        $items = [];
                        $articuls = [];
                        if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] !== '') {
                            $inputFileName = $_FILES['file']['tmp_name'];
                            $inputFileType = \PHPExcel_IOFactory::identify(
                                $inputFileName
                            );
                            $objReader = \PHPExcel_IOFactory::createReader(
                                $inputFileType
                            );
                            $objPHPExcel = $objReader->load($inputFileName);
                            $sheetData = $objPHPExcel->getActiveSheet()
                                ->toArray(null, false, false, false);
                            $articuls = array_map(
                                function ($el) use ($articuls) {
                                    return $el[0];
                                },
                                $sheetData
                            );
                        } else {
                            $articuls = array_filter(
                                array_unique(
                                    preg_split('/\s/', $request->articuls)
                                ),
                                function ($el) {
                                    return $el !== "";
                                }
                            );
                        }

                        if (count($articuls)) {
                            $items = $catalog->getItems(
                                ['articul' => $articuls]
                            );
                        }

                        $app->tpl->assign('items', $items);
                        break;
                    case 'process':
                        $items = $request->item;
                        $operation = $request->operation;
                        if (count($items)) {
                            switch ($operation) {
                                case "stock_stock":
                                case "stock_order":
                                case "stock_none":
                                    $stock = substr($operation, 6);
                                    $catalog->setItemStock($items, $stock);
                                    break;
                                case "visibility_1":
                                case "visibility_0":
                                    $flag = substr(
                                        $operation,
                                        11
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemActive($items, $flag);
                                    break;
                                case "price_warn_1":
                                case "price_warn_0":
                                    $flag = substr(
                                        $operation,
                                        11
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemPriceWarn($items, $flag);
                                    break;
                                case "new_1":
                                case "new_0":
                                    $flag = substr(
                                        $operation,
                                        4
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemFlagNew($items, $flag);
                                    break;
                                case "top_1":
                                case "top_0":
                                    $flag = substr(
                                        $operation,
                                        4
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemFlagTop($items, $flag);
                                    break;
                                case "special_1":
                                case "special_0":
                                    $flag = substr(
                                        $operation,
                                        8
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemFlagSpecial($items, $flag);
                                    break;
                                case "commission_1":
                                case "commission_0":
                                    $flag = substr(
                                        $operation,
                                        11
                                    ) == '1' ? 1 : 0;
                                    $catalog->setItemFlagCommission(
                                        $items,
                                        $flag
                                    );
                                    break;
                                case "delete":
                                    $catalog->deleteItem($items);
                                    break;
                                case "noop":
                                default:
                                    break;
                            }
                        }
                        $response->redirect(
                            '/admin/catalog/import/batch/success'
                        )->send();
                        break;
                }
        }
        $app->site->template = 'admin/admin-catalog-import';
        $app->tpl->display('layouts/admin-catalog.tpl');
    }
);

$this->respond(
    'GET',
    '/catalog/export',
    function ($request, $response, $service, $app) use ($catalog) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export-'.date("Y-m-d").'.csv"');
        header('Content-Transfer-Encoding: binary');

        $items = $catalog->getItems(['active' => true, 'features' => true]);
        $out = fopen('php://output', 'w');
        fputs($out, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));
        foreach ($items as $i) {
            fputcsv(
                $out,
                [
                    $i['title'],
                    $i['articul'],
                    (isset($i['features'])) ? implode(
                        "; ",
                        array_map(
                            function ($f) { return $f['feature_title'].': '.$f['feature_value'].' '.$f['feature_unit']; },
                            $i['features']
                        )
                    ) : "",
                ],
                ';'
            );
        }
        fclose($out);
        die();
    }
);

$this->respond(
    'GET',
    '/catalog/qcc/check/?',
    function ($request, \Klein\Response $response) use ($qcc) {
        $response->json(['status' => $qcc->check(),]);
    }
);
