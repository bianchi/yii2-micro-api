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
    
    public function rules()
    {
        return [
            [['id', 'service_id', 'current_status_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Order::find();

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

        return $dataProvider;
    }
}