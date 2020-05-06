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
                        'PATCH,OPTIONS {id}/password' => 'change-password'
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'customer',
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'order', 
                    'extraPatterns' => [
                        'GET,OPTIONS stats' => 'stats',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'invoice', 
                    'extraPatterns' => [
                        'GET,OPTIONS stats' => 'stats',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'password-reset',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET {token}' => 'view',
                        'PATCH {token}' => 'change-password'
                    ],
                    'tokens' => [
                        '{token}' => '<token:\\w+>',
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
