<?php

$config = [
    'id' => 'cnrdoc',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    // set an alias to enable autoloading of classes from the 'api' namespace
    'aliases' => [
        '@api' => dirname(__DIR__),
    ],
    'language' => 'pt-BR',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'user', 
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'GET {user_id}/orders' => 'orders'
                    ],
                    'tokens' => [
                        // '{id}' => '<id:\\w+>',
                        // '{user_id}' => '<user_id:\\w+>'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'customer',
                    'extraPatterns' => [
                        'GET {customer_id}/orders' => 'orders',
                        'GET {customer_id}/orders/stats' => 'orders-stats',
                        'GET {customer_id}/invoices' => 'invoices',
                        'GET {customer_id}/invoices/stats' => 'invoices-stats'
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\w+>',
                        '{customer_id}' => '<customer_id:\\w+>'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'order',
                ],
            ],
        ],
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => \yii\web\Response::class,
            'format' => \yii\web\Response::FORMAT_JSON,
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'loginUrl' => null,
            'enableSession' => false
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/templates/email',
            'useFileTransport' => YII_ENV_DEV,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'host',
                'username' => 'username',
                'password' => 'password',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];

$db = require __DIR__ . '/database.php';
$config['components']['db'] = $db;

return $config;
