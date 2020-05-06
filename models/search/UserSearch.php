<?php

namespace api\models\search;

use api\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model 
{
    public $customer_id;
    
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        $loggedUser = User::findOne(\Yii::$app->user->id);
        $this->customer_id = $loggedUser->customer_id;

        $query->andWhere(['customer_id' => $this->customer_id]);

        return $dataProvider;
    }
}