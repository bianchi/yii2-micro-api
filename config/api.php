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
                    'controller' => 'account',
                    'patterns' => [
                        'POST' => 'create',
                        'GET' => 'index',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'password-reset',
                    'pluralize' => false,
                    'patterns' => [
                        'POST' => 'create',
                        'GET {token}' => 'view',
                        'PATCH {token}' => 'change-password'
                    ],
                    'tokens' => [
                        '{token}' => '<token:\\w+>',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => ['login' => 'user'],
                    'pluralize' => false,
                    'patterns' => [
                        'POST' => 'login',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'user', 
                    'patterns' => [
                        'GET' => 'index',
                        'POST' => 'create',
                        'GET {id}' => 'view',
                        'PATCH {id}' => 'update',
                        'PATCH,OPTIONS {id}/password' => 'change-password'
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'customer',
                    'patterns' => [
                        'POST' => 'create',
                        'GET {id}' => 'view',
                        'PATCH {id}' => 'update',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'order', 
                    'patterns' => [
                        'GET' => 'index',
                        'GET {id}' => 'view',
                        'GET stats' => 'stats',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'invoice', 
                    'patterns' => [
                        'GET' => 'index',
                        'GET {id}' => 'view',
                        'GET stats' => 'stats',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'service', 
                    'patterns' => [
                        'GET' => 'index',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'federative-unit', 
                    'patterns' => [
                        'GET {uf}' => 'cities',
                    ],
                    'tokens' => [
                        '{uf}' => '<uf:[a-zA-Z]{2}>',
                    ]
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
            // 'useFileTransport' => YII_ENV_DEV,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'cbrdoc.test@gmail.com',
                'password' => 'cbrdoc.test123',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];

$db = require __DIR__ . '/database.php';
$config['components']['db'] = $db;

return $config;
