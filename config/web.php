<?php

use yii\web\Response;

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
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
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:(v1)>/<controller:[\w\-]+>/<action:[\w\-]+>/<parent_id:\d+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:(v1)>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:(v1)>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
                '<module:(v1)>/<controller:[\w\-]+>' => '<module>/<controller>/index',
                //
                '<controller:\w+>/<action:\w+>/<parent_id:\d+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<parent_id:\d+>' => '<controller>/<action>',
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
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;