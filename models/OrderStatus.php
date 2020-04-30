<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "order_statuses".
 *
 * @property int $id
 * @property string $name
 *
 * @property OrderHistory[] $orderHistories
 * @property Orders[] $orders
 */
class OrderStatus extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'order_statuses';
    }

    /**
     * Model validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * Label for each attribute. It will be used in errors
     * 
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['current_status_id' => 'id']);
    }
}
