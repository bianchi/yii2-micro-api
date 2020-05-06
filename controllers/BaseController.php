<?php 

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use api\models\User;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;

/**
 * @OA\Info(title="CBR Docs", version="0.1")
 */
class BaseController extends ActiveController
{
    public function beforeAction($action)
    {
        $noAuthenticationRoutes = [
            'user/login',
            'customer/create',
            'password-reset/create',
            'password-reset/view'
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        $requestedRoute = $action->controller->module->requestedRoute;

        // if requested route needs authentication, checks token
        if (!in_array($requestedRoute, $noAuthenticationRoutes)) {
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
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            // 'cors' => [
            //     [
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

        unset($actions['index'], $actions['delete'], $actions['update'], $actions['view'], $actions['create']);

        return $actions;
    }
}