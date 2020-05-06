<?php 

namespace api\controllers;

use api\models\Customer;
use api\models\User;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

class CustomerController extends BaseController
{
    public $modelClass = 'api\models\Customer';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'][] = 'create';

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
    }

    public function actionCreate() 
    {
        $model = new Customer();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            $response = \Yii::$app->getResponse()->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['/customers/' . $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionView($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        if ($id != $loggedUser->customer_id) {
            throw new ForbiddenHttpException("Can't view another customer besides yours");
        }

        $model = Customer::findOne(['id' => $id]);
        if ($model == null) {
            throw new NotFoundHttpException("Customer not found");
        }

        $this->checkAccess($this->action->id, $model);
        
        return $model;
    }

    public function actionUpdate($id) 
    {
        $loggedUser = User::findOne(\Yii::$app->user->id);

        if ($id != $loggedUser->customer_id) {
            throw new ForbiddenHttpException("Can't update another customer besides yours");
        }

        $model = Customer::findOne(['id' => $id]);
        if ($model == null) {
            throw new NotFoundHttpException("Customer not found");
        }
        
        $this->checkAccess($this->action->id, $model);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}