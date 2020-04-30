<?php 

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use api\models\User;

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
                throw new ForbiddenHttpException("Token has expired, please login again.");
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
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login'],
        ];
        
        return $behaviors;
    }
}