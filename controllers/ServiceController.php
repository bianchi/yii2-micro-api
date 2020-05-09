<?php

namespace api\controllers;

use api\models\search\ServiceSearch;

class ServiceController extends BaseController
{
    public $modelClass = 'api\models\Service';

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new ServiceSearch;

        return $searchModel->search(\Yii::$app->request->get());
    }
}