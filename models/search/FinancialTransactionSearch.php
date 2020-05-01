<?php

namespace api\models\search;

use api\models\Order;
use app\models\FinancialTransaction;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FinancialTransactionSearch extends Model 
{
    public $order_id;
    public $operation;
    public $customer_name;
    
    public function rules()
    {
        return [
            [['order_id'], 'integer'],
            [['operation'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = FinancialTransaction::find()->alias('ft')
            ->joinWith(['customer c'])
            ->joinWith(['user u'])
            ->joinWith(['order o']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['operation' => $this->operation]);
        $query->andFilterWhere(['order_id' => $this->order_id]);
        $query->andFilterWhere(['c.name' => $this->customer_name]);

        return $dataProvider;
    }
}