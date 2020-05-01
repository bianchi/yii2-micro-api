<?php 

namespace api\controllers;

use api\models\Order;
use api\models\search\OrderSearch;
use api\models\User;
use yii\web\ForbiddenHttpException;

class CompanyController extends BaseController
{
    public $modelClass = 'api\models\Company';

    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action == 'orders') {
            if (!$user->is_admin) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to list orders from whole company, it\'s not an admin');
            }

            if ($params['company_id'] != $user->company_id) {
                throw new ForbiddenHttpException('Users can only list orders from its company');
            }
        }
    }

    /**
     * List orders from $company_id company
     *
     * @param int $company_id the ID of the company to search orders
     * @return Order[] from that company
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function actionOrders($company_id)
    {
        $this->checkAccess($this->action->id, null, ['company_id' => $company_id]);

        $searchModel = new OrderSearch;    
        
        return $searchModel->search(\Yii::$app->request->get());
    }
}