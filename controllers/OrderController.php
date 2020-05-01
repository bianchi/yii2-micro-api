<?php 

namespace api\controllers;

class OrderController extends BaseController
{
    public $modelClass = 'api\models\Order';

    /**
     * Disable order/index 
     * @return array $actions
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }
}