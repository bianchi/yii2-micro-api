<?php

namespace api\controllers;

use api\models\DocumentType;
use api\models\search\DocumentTypeSearch;

class DocumentTypeController extends BaseController
{
    public $modelClass = 'api\models\DocumentType';

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new DocumentTypeSearch;

        return $searchModel->search(\Yii::$app->request->get());
    }
}