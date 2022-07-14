<?php

require __DIR__ . '/config.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/classes/database.php';
require __DIR__ . '/classes/application.php';
require __DIR__ . '/classes/account.php';
require __DIR__ . '/classes/controller.php';
require __DIR__ . '/classes/site.php';
require __DIR__ . '/classes/stats.php';
require __DIR__ . '/classes/helpers.php';
require __DIR__ . '/classes/captcha.php';
require __DIR__ . '/classes/mailer.php';
require __DIR__ . '/classes/validation.php';

require __DIR__ . '/../vendor/blueimp/jquery-file-upload/server/php/UploadHandler.php';

/**
 * Controllers
 */
require __DIR__ . '/controllers/accountController.php';
require __DIR__ . '/controllers/ordersController.php';
require __DIR__ . '/controllers/reviewsController.php';
require __DIR__ . '/controllers/keywordsController.php';
require __DIR__ . '/controllers/catalogController.php';
require __DIR__ . '/controllers/contentController.php';
require __DIR__ . '/controllers/uploadController.php';
require __DIR__ . '/controllers/usersController.php';
require __DIR__ . '/controllers/statsController.php';
require __DIR__ . '/controllers/qccController.php';
require __DIR__ . '/controllers/callbackController.php';
require __DIR__ . '/controllers/promoController.php';

setlocale(LC_ALL, 'ru_RU', 'ru', 'ru_RU.UTF8', 'ru_RU.UTF-8', 'Russian', 'Russia');
