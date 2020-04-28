<?php 

namespace api\controllers;

use yii\rest\ActiveController;

class CompanyController extends ActiveController
{
    public $modelClass = 'api\models\Company';

    public function behaviors()
    {
        // remove rateLimiter which requires an authenticated user to work
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }
}