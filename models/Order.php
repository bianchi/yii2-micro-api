<?php

namespace api\models;

use Yii;
use api\models\User;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int $current_status_id
 * @property date $placed_time
 *
 * @property OrderHistory[] $orderHistories
 * @property OrderStatuses $currentStatus
 * @property Service $service
 * @property User $user
 * @property Customer $customer
 */
class Order extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * Model validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'service_id', 'current_status_id'], 'required'],
            [['user_id', 'service_id', 'current_status_id', 'service_subtype_id'], 'integer'],
            [['placed_time'], 'safe'],
            [['current_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['current_status_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['service_subtype_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceSubtype::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'current_status_id' => 'Current Status ID',
            'placed_time' => 'Placed Time',
        ];
    }

    /**
     * Return an array of relations that can be expanded e.g. /order/5?expand=user
     * 
     * @return array
     */
    public function extraFields()
    {
        return ['histories', 'currentStatus', 'service', 'user', 'customer'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistories()
    {
        return $this->hasMany(OrderHistory::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'current_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
