<?php

namespace api\controllers;

use api\models\User;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\UnauthorizedHttpException;

class BaseController extends ActiveController
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $noAuthenticationRoutes = [
            'account/create',
            'user/login',
            'password-reset/create',
            'password-reset/view',
            'password-reset/change-password',
            'federative-unit/index',
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        $requestedRoute = $action->controller->module->requestedRoute;

        // if requested route needs authentication, checks token
        if ($action->id != 'options' && !in_array($requestedRoute, $noAuthenticationRoutes)) {
            $user = User::findOne(\Yii::$app->user->id);

            if ($user == null) {
                throw new UnauthorizedHttpException('No logged user');
            }

            $currentDate = new \Datetime;
            $tokenTimeoutDate =  new \Datetime($user->last_api_request);
            $tokenTimeoutDate->modify('+' . User::LOGIN_TOKEN_TIMEOUT_MINUTES . ' minutes');

            $tokenExpirationDate =new \Datetime($user->last_login);
            $tokenExpirationDate->modify('+' . User::LOGIN_TOKEN_MAX_DURATION_MINUTES . ' minutes');

            if ($currentDate > $tokenTimeoutDate) {
                throw new UnauthorizedHttpException("Token has expired by inactivity, please login again.");
            } elseif ($currentDate > $tokenExpirationDate) {
                throw new UnauthorizedHttpException("Token has expired by max duration, please login again.");
            } else {
                $user->updateAttributes([
                    'last_api_request' => $currentDate->format('Y-m-d H:i:s'),
                ]);
            }
        }   
        
        return true;
    }

    public static function allowedDomains()
    {
        return [
            // '*',                        // star allows all domains
            'http://localhost:3000',
            'http://localhost:5000',
            'http://localhost:8000',
            'https://cbrdoc.netlify.app',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => static::allowedDomains(),
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page'],
            ],
        ];
        $behaviors['authenticator'] = $auth;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'options'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // remove framework default actions
        unset($actions['index'], $actions['delete'], $actions['update'], $actions['view'], $actions['create']);

        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];

        return $actions;
    }
}
