<?php 

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use api\models\User;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;

class BaseController extends ActiveController
{
    public function beforeAction($action)
    {
        if ($action->id == 'options') {
            return parent::beforeAction($action);
        }

        $noAuthenticationRoutes = [
            'account/create',
            'user/login',
            'password-reset/create',
            'password-reset/view',
            'password-reset/change-password',
            'federative-unit/index',
        ];

        $requestedRoute = $action->controller->module->requestedRoute;

        if (in_array($requestedRoute, $noAuthenticationRoutes)) {
            return parent::beforeAction($action);
        }

        // if requested route needs authentication, checks token
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
        
        return parent::beforeAction($action);
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            // 'cors' => [
            //     [
            //         'Access-Control-Allow-Credentials' => true,
            //         'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count','X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page'],
            //     ]
            // ]
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