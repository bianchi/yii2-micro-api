<?php 

namespace api\controllers;

use api\models\Order;
use api\models\search\FinancialTransactionSearch;
use api\models\search\OrderSearch;
use api\models\User;
use app\models\FinancialTransaction;
use yii\web\ForbiddenHttpException;

class CustomerController extends BaseController
{
    public $modelClass = 'api\models\Customer';

    /**
     * Disable customer/delete and customer/index
     * @return array $actions
     */
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" action in this endpoint
        unset($actions['delete'], $actions['index']);

        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action == 'orders') {
            if (!$user->is_admin) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to list all orders from the customer, it\'s not an admin');
            }

            if ($params['customer_id'] != $user->customer_id) {
                throw new ForbiddenHttpException('Users can only list orders from its own customer');
            }
        }
    }

    /**
     * List orders from $customer_id customer
     *
     * @param int $customer_id the ID of the customer to search orders
     * @return Order[] from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionOrders($customer_id)
    {
        $this->checkAccess($this->action->id, null, ['customer_id' => $customer_id]);

        $searchModel = new OrderSearch;    
        
        return $searchModel->search(\Yii::$app->request->get());
    }

    /**
     * List financial transactions from $customer_id customer
     *
     * @param int $customer_id the ID of the customer to search financial transactions
     * @return FinancialTransaction[] from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionFinancialTransactions($customer_id)
    {
        $this->checkAccess($this->action->id, null, ['customer_id' => $customer_id]);

        $searchModel = new FinancialTransactionSearch;    
        
        return $searchModel->search(\Yii::$app->request->get());
    }
}