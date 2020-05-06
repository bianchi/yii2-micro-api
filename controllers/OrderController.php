<?php

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\User;
use yii\web\NotFoundHttpException;

class OrderController extends BaseController
{
    public $modelClass = 'api\models\Order';

    /**
     * Checks the privilege of the current user.
     *
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
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

    /**
     * Calculate orders statistics for $customer_id customer
     *
     * @param int $customer_id the ID of the customer to search orders statistics
     * @param int|null $month desired month, if null will be current month
     * @param int|null $year desired year, if null will be current year
     * @return array[] orders statistic from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionStats()
    {
        $this->checkAccess($this->action->id, null);

        return [];
    }
}