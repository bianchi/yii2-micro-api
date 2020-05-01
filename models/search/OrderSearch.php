<?php

namespace api\models\search;

use api\models\Order;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSearch extends Model 
{
    public $id;
    public $document_type_id;
    public $current_status_id;
    public $customer_id;
    public $user_id;
    
    public function rules()
    {
        return [
            [['id', 'customer_id', 'user_id', 'document_type_id', 'current_status_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Order::find()->alias('o')->joinWith('user u');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['o.id' => $this->id]);
        $query->andFilterWhere(['user_id' => $this->user_id]);
        $query->andFilterWhere(['u.customer_id' => $this->customer_id]);
        $query->andFilterWhere(['current_status_id' => $this->current_status_id]);

        return $dataProvider;
    }
}