<?php

namespace api\models\search;

use api\models\Order;
use api\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSearch extends Model
{
    public $id;
    public $service_id;
    public $current_status_id;
    public $begin_date;
    public $end_date;
    public $current_status_name;
    public $count;

    public function rules()
    {
        return [
            [['id', 'service_id', 'current_status_id', 'count'], 'integer'],
            [['begin_date', 'end_date'], 'date', 'format' => 'Y-m-d'],
            [['current_status_name'], 'string'],
            [['count'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Order::find()->alias('o');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $loggedUser = User::findOne(\Yii::$app->user->id);
        if ($loggedUser->is_admin) {
            $query->andWhere(['customer_id' => $loggedUser->customer_id]);
        } else {
            $query->andWhere(['user_id' => $loggedUser->id]);
        }

        // if an invalid parameter is send, return without using any parameters
        if (!empty($params) && !($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['o.id' => $this->id]);
        $query->andFilterWhere(['current_status_id' => $this->current_status_id]);

        if (!empty($this->begin_date) && !empty($this->end_date)) {
            $query->andFilterWhere(['between', 'ft.placed_time', $this->begin_date, $this->end_date]);
        }

        return $dataProvider;
    }

    public function searchStats($params)
    {
        $dataProvider = $this->search($params);

        $dataProvider->query->joinWith(['currentStatus cs']);
        $dataProvider->query->select(['current_status_id', 'cs.name AS current_status_name', 'COUNT(*) AS count']);
        $dataProvider->query->groupBy(['current_status_id']);

        return $dataProvider->query->createCommand()->queryAll();
    }
}
