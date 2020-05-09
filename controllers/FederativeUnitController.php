<?php

namespace api\controllers;

use api\models\backoffice\Api;
use api\models\User;

class FederativeUnitController extends BaseController
{
    public $modelClass = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'][] = 'index';

        return $behaviors;
    }

    public function actionCities($uf)
    {
        $uf = strtoupper($uf);
        $loggedUser = User::findOne(\Yii::$app->user->id);

        // $backofficeApi = new Api($loggedUser->customer->backoffice_email, $loggedUser->customer->backoffice_password);
        $backofficeApi = new Api('empresa@teste.com', 'Emp@1234!');
        
        $cities = $backofficeApi->getCities($uf);

        echo "<pre>";
        print_r($cities);
        echo "</pre>";
        exit();
    }
}