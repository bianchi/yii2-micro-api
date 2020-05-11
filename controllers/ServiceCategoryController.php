<?php

namespace api\controllers;

use app\models\ServiceCategory;

class ServiceCategoryController extends BaseController
{
    public $modelClass = 'api\models\ServiceCategory';

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        return ServiceCategory::find()->all();
    }
}