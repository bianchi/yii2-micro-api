<?php

namespace api\models\search;

use app\models\Invoice;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoiceSearch extends Model 
{
    public $order_id;
    public $operation;
    public $customer_name;
    public $approved_only;
    public $month;
    public $year;
    public $begin_date;
    public $end_date;
    
    public function rules()
    {
        return [
            [['order_id', 'month', 'year'], 'integer'],
            [['operation'], 'string'],
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

        if ($this->approved_only) {
            $query->andWhere(['IS NOT', 'approved_time', null]);
        }

        if (!empty($this->month) && !empty($this->year)) {
            $this->begin_date = (new \Datetime($this->year . '-' . $this->month . '-01'))->setTime(0, 0, 0);
            $this->end_date = (clone $this->begin_date)->modify('last day of this month')->setTime(23, 59, 59);

            $this->begin_date = $this->begin_date->format('Y-m-d H:i:s');
            $this->end_date = $this->end_date->format('Y-m-d H:i:s');
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