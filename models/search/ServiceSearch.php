<?php

namespace api\models\search;

use api\models\Service;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ServiceSearch extends Model 
{
    public $category_id;
    
    public function rules()
    {
        return [
            [['category_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Service::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if an invalid parameter is send, return without using any parameters
        if (!empty($params) && !($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['category_id' => $this->category_id]);

        return $dataProvider;
    }
}