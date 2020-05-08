<?php

namespace api\controllers;

use api\models\Customer;

class AccountController extends BaseController
{
    public $modelClass = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'][] = 'create';

        return $behaviors;
    }

    public function actionCreate()
    {
        $body = \Yii::$app->getRequest()->getBodyParams();

        echo "<pre>";
        print_r($body);
        echo "</pre>";
        exit();
        
        // $customer = new Customer;
        // $customer->name = $body['customer_name'];
        // $customer->name = $body['customer_entity_type'];
    }
}