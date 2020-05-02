<?php 

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use api\models\User;
use api\cors\CorsPreFlight;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;

class BaseController extends ActiveController
{
    const TOKEN_DURATION_MINUTES = 600;

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if ($action->id != 'login') {
            $user = User::findOne(\Yii::$app->user->id);

            $currentDate = new \Datetime;
            $tokenExpirationDate = new \Datetime($user->last_api_request);
            $tokenExpirationDate->modify('+' . self::TOKEN_DURATION_MINUTES . ' minutes');

            if ($currentDate > $tokenExpirationDate) {
                throw new UnauthorizedHttpException("Token has expired, please login again.");
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
            'cors' => [
                [
                    'Origin' => ['*'], 
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], 
                    'Access-Control-Request-Headers' => ['*'], 
                    'Access-Control-Allow-Credentials' => null, 
                    'Access-Control-Max-Age' => 86400, 
                    'Access-Control-Allow-Headers' => ['X-Pagination-Total-Count','X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page'],
                    'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count','X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page'],
                ]
            ]
        ];

        $behaviors['authenticator'] = $auth;
        $behaviors['authenticator']['except'] = ['options'];
        
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'options'],
        ];

        return $behaviors;
    }
}