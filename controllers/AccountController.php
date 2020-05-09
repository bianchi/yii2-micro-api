<?php

namespace api\controllers;

use api\models\Customer;
use api\models\forms\Account;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

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
        $account = new Account;
        $account->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($account->save()) {
            $response = \Yii::$app->getResponse()->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['/users/' . $account->user_id], true));
        } elseif (!$account->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $account;
    }
}