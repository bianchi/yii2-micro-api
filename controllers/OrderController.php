<?php 

namespace api\controllers;

use api\models\User;
use yii\web\ForbiddenHttpException;

class OrderController extends BaseController
{
    public $modelClass = 'api\models\Order';

    public function checkAccess($action, $model = null, $params = [])
    {
        $user = User::findOne(\Yii::$app->user->id);

        if ($action == 'view') {
            if ($model->user->customer_id != $user->customer_id) {
                throw new ForbiddenHttpException('Users can only view orders from its customer');
            }
            
            if (!$user->is_admin && $model->user_id != $user->id) {
                throw new ForbiddenHttpException('Current logged user don\'t have permission to view another user orders, it\'s not an admin');
            }
        }
    }

    /**
     * Disable order/index, order/update, order/delete
     * @return array $actions
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update'], $actions['index']);
        return $actions;
    }
}