<?php 

namespace api\controllers;

use api\models\Order;
use api\models\OrderStatus;
use api\models\search\InvoiceSearch;
use api\models\search\OrderSearch;
use api\models\User;
use app\models\Invoice;
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

        if ($action == 'orders' || $action == 'orders-statistics') {
            if ($params['customer_id'] != $user->customer_id) {
                throw new ForbiddenHttpException('Users can only list orders data from its own customer');
            }

            if (!$user->is_admin) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to list all orders from the customer, and it\'s not an admin');
            }
        }

        if ($action == 'financial-transactions' || $action == 'financial-statistics') {
            if ($params['customer_id'] != $user->customer_id) {
                throw new ForbiddenHttpException('Users can only list financial transaction data from its own customer');
            }

            if (!$user->can_see_invoices && !$user->is_admin) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to list financial transaction data, and it\'s not an admin');
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
     * List Invoices from $customer_id customer
     *
     * @param int $customer_id the ID of the customer to search Invoices
     * @return Invoice[] from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionInvoices($customer_id)
    {
        $this->checkAccess($this->action->id, null, ['customer_id' => $customer_id]);

        $searchModel = new InvoiceSearch;    
        
        return $searchModel->search(\Yii::$app->request->get());
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
    public function actionOrdersStats($customer_id, $month = null, $year = null)
    {
        $this->checkAccess($this->action->id, null, ['customer_id' => $customer_id]);

        $month = $month ?: date('m');
        $year = $year ?: date('Y');

        $beginDate = new \Datetime($year . '-' . $month . '-01');
        $endDate = (clone $beginDate)->modify('last day of this month');

        $groupedOrders = Order::find()->alias('o')
        ->joinWith(['currentStatus as cs'])
        ->select('count(o.id) as count, cs.name as status')
        ->where(['between', 'placed_time', $beginDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')])
        ->groupBy(['o.current_status_id'])
        ->createCommand()->queryAll();


        return $groupedOrders;
    }

     /**
     * Calculate invoices statistics for $customer_id customer
     *
     * @param int $customer_id the ID of the customer to search invoices statistics
     * @return array[] invoices statistic from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionInvoicesStats($customer_id)
    {
        $this->checkAccess($this->action->id, null, ['customer_id' => $customer_id]);

        $searchModel = new InvoiceSearch;    

        $params = \Yii::$app->request->get();

        if (empty($params['operation'])) {
            $params['operation'] = Invoice::OPERATION_CREDIT;
            $creditsStats = $searchModel->searchStats($params);
            
            $params['operation'] = Invoice::OPERATION_DEBIT;
            $debitsStats = $searchModel->searchStats($params);
    
            return [
                'credits' => $creditsStats,
                'debits' => $debitsStats,
            ];
        } else {
            if ($params['operation'] == Invoice::OPERATION_CREDIT) {
                return ['credits' => $searchModel->searchStats($params)];
            } else if ($params['operation'] == Invoice::OPERATION_DEBIT) {
                return ['debits' => $searchModel->searchStats($params)];
            }
        }
    }
}