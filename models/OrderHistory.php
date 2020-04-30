<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "order_history".
 *
 * @property int $order_id
 * @property int $status_id
 * @property string $event_time
 *
 * @property Orders $order
 * @property OrderStatuses $status
 */
class OrderHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'order_history';
    }

    /**
    * Model validation rules
    * 
    * @return array
    */
    public function rules()
    {
        return [
            [['order_id', 'status_id'], 'required'],
            [['order_id', 'status_id'], 'integer'],
            [['event_time'], 'safe'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
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
            'order_id' => 'Order ID',
            'status_id' => 'Status ID',
            'event_time' => 'Event Time',
        ];
    }

    /**
     * Return an array of relations that can be expanded e.g. /order/5?expand=user
     * 
     * @return array
     */
    public function extraFields()
    {
        return ['order', 'status'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
    }
}
