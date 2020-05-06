<?php

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\User;
use yii\web\NotFoundHttpException;

class OrderController extends BaseController
{
    public $modelClass = 'api\models\Order';

    public function checkAccess($action, $model = null, $params = [])
    {
    }

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new OrderSearch;

        return $searchModel->search(\Yii::$app->request->get());
    }

    public function actionView($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        $model = Order::findOne(['id' => $id, 'customer_id' => $loggedUser->customer_id]);
        if ($model == null) {
            throw new NotFoundHttpException("Order not found");
        }

        $this->checkAccess($this->action->id, $model);
        
        return $model;
    }

    public function actionStats()
    {
        $this->checkAccess($this->action->id, null);

        return [];
    }
}