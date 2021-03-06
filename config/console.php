<?php

$config = [
    'id' => 'cnrdoc',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    // set an alias to enable autoloading of classes from the 'api' namespace
    'aliases' => [
        '@api' => dirname(__DIR__),
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => ['@api/migrations']
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

$db = require __DIR__ . '/database.php';
$config['components']['db'] = $db;

return $config;