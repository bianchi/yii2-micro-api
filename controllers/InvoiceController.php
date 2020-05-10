<?php

namespace api\controllers;

use api\models\forms\CreditCard;
use api\models\forms\WalletDeposit;
use api\models\search\InvoiceSearch;
use api\models\User;
use api\models\Invoice;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class InvoiceController extends BaseController
{
    public $modelClass = 'api\models\Invoice';

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
        $user = User::findOne(\Yii::$app->user->id);

        if ($action === 'index' && !$user->is_admin && !$user->can_see_invoices) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to list invoices and it\'s not an admin');
        }

        if ($action === 'stats' && !$user->is_admin && !$user->can_see_invoices) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to list invoices and it\'s not an admin');
        }

        if ($action === 'insert_credits' && !$user->is_admin && !$user->can_insert_credits) {
            throw new ForbiddenHttpException('Current logged user don\'t have permission to insert credits and it\'s not an admin');
        }
    }

    public function actionIndex()
    {
        $this->checkAccess($this->action->id, null);

        $searchModel = new InvoiceSearch();    
        
        return $searchModel->search(\Yii::$app->request->get());
    }

    public function actionStats()
    {
        $this->checkAccess($this->action->id, null);

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

    public function actionInsertCredits()
    {
        $this->checkAccess($this->action->id);

        $walletDeposit = new WalletDeposit;
        $walletDeposit->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($walletDeposit->save()) {
            $response = \Yii::$app->getResponse()->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['/invoices/' . $walletDeposit->invoice_id], true));
        } elseif (!$walletDeposit->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $walletDeposit;
    }
}