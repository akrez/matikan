<?php
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'vendorPath' => '../../vendor',
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => $db,
    ],
    'params' => $params,
];

return $config;
