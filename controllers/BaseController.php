<?php 

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use api\models\User;
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

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                // restrict access to
                'Origin' => ['*', '*:5000', 'http://localhost:5000', 'https://cbrdoc.netlify.app'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],

        ];
        
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'options'],
        ];

        return $behaviors;
    }
}