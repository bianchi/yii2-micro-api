<?php

namespace api\controllers;

use api\models\search\InvoiceSearch;
use api\models\User;
use app\models\Invoice;
use yii\web\NotFoundHttpException;

class InvoiceController extends BaseController
{
    public $modelClass = 'api\models\Invoice';

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new InvoiceSearch();    
        
        return $searchModel->search(\Yii::$app->request->get());
    }

     /**
     * Calculate invoices statistics for $customer_id customer
     *
     * @return array[] invoices statistic from that customer
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionStats()
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