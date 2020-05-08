<?php

namespace api\models\search;

use api\models\Invoice;
use yii\base\Model;
use api\models\User;
use yii\data\ActiveDataProvider;

class InvoiceSearch extends Model 
{
    public $order_id;
    public $operation;
    public $customer_name;
    public $approved_only;
    public $begin_date;
    public $end_date;
    
    public function rules()
    {
        return [
            [['order_id'], 'integer'],
            [['operation'], 'string'],
            [['begin_date', 'end_date'], 'date', 'format' => 'Y-m-d'],
            [['approved_only'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true]
        ];
    }

    public function beforeValidate() 
    {
        $this->approved_only = filter_var($this->approved_only, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }

    public function search($params)
    {
        $query = Invoice::find()->alias('ft')
            ->joinWith(['customer c'])
            ->joinWith(['order o']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $loggedUser = User::findOne(\Yii::$app->user->id);
        if ($loggedUser->is_admin) {
            $query->andWhere(['ft.customer_id' => $loggedUser->customer_id]);
        } else {
            $query->andWhere(['user_id' => $loggedUser->id]);
        }

        if (!empty($params) && !($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['operation' => $this->operation]);
        $query->andFilterWhere(['order_id' => $this->order_id]);
        $query->andFilterWhere(['c.name' => $this->customer_name]);

        if ($this->approved_only) {
            $query->andWhere(['IS NOT', 'approved_time', null]);
        }

        if (!empty($this->begin_date) && !empty($this->end_date)) {
            $query->andFilterWhere(['between', 'ft.placed_time', $this->begin_date, $this->end_date]);
        }

        return $dataProvider;
    }

    public function searchStats($params)
    {
        $dataProvider = $this->search($params);

        return [
            'sum' => doubleval($dataProvider->query->sum('amount')),
            'count' => $dataProvider->query->count()
        ];
    }
}