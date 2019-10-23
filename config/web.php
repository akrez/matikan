<?php

use yii\web\Response;

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$mailer = require(__DIR__ . '/mailer.php');

return [
    'id' => 'basic',
    'name' => 'ماتیکان',
    'language' => 'fa-IR',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'vendorPath' => '../../vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            'enableCsrfValidation' => false,
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            'view' => (YII_DEBUG ? '@yii/views/errorHandler/exception.php' : '@yii/views/errorHandler/error.php' ),
        ],
        'db' => $db,
        'mailer' => $mailer,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET <module:(v1)>/<controller:[\w\-]+>' => '<module>/<controller>/index',
                'POST <module:(v1)>/<controller:[\w\-]+>' => '<module>/<controller>/create',
                'GET <module:(v1)>/<controller:[\w\-]+>/<id:\d+>' => '<module>/<controller>/view',
                'POST <module:(v1)>/<controller:[\w\-]+>/<id:\d+>' => '<module>/<controller>/update',
                'DELETE <module:(v1)>/<controller:[\w\-]+>/<id:\d+>' => '<module>/<controller>/delete',
                //
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>' => '<controller>/index',
            ],
        ],
        'response' => [
            'class' => Response::class,
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $statusCode = $event->sender->statusCode;
                if ($statusCode != 200 && !YII_DEBUG) {
                    $event->sender->data = ['code' => $statusCode];
                } elseif (isset($event->sender->data['code']) && !YII_DEBUG) {
                    $event->sender->data = ['code' => $event->sender->data['code']];
                } else {
                    $event->sender->data = (array) $event->sender->data;
                    $event->sender->data['code'] = $statusCode;
                }
            },
        ],
    ],
    'params' => $params,
];
