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
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
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
                '<controller:[\w\-]+>/<action:[\w\-]+>/<parent_id:\d+>/<id:\d+>' => '<controller>/<action>',
                '<controller:[\w\-]+>/<action:[\w\-]+>/<parent_id:\d+>' => '<controller>/<action>',
                '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
                '<controller:[\w\-]+>' => '<controller>/index',
            ],
        ],
        'response' => [
            'class' => Response::class,
            'format' => Response::FORMAT_HTML,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                if (in_array($event->sender->format, [Response::FORMAT_JSON, Response::FORMAT_JSONP, Response::FORMAT_XML])) {
                    $code = $event->sender->statusCode;
                    $event->sender->data = ( $code == 200 || YII_ENV_DEV ? (array) $event->sender->data : [] );
                    $event->sender->data['code'] = $code;
                }
            },
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
