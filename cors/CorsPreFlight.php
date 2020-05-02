<?php

namespace api\cors;

use yii\filters\Cors;
use Yii;

class CorsPreFlight extends Cors
{
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->end();
        }
        return true;
    }
}