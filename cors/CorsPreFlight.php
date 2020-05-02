<?php

namespace api\cors;

use yii\filters\Cors;

class CorsPreFlight extends Cors
{
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()->getHeaders()->set('Allow', 'POST GET PUT');
            Yii::$app->end();
        }
        return true;
    }
}