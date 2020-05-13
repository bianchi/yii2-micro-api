<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $service_id
 * @property int $current_status_id
 * @property string $name
 * @property int $priority
 * @property string $placed_time
 * @property string $estimated_time
 * @property string $delivered_time
 * @property string $rejected_reason
 * @property string $annotations
 *
 * @property Invoices[] $invoices
 * @property OrderHistory[] $orderHistories
 * @property OrderStatuses $currentStatus
 * @property Customers $customer
 * @property Services $service
 * @property Users $user
 */
class Temp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'user_id', 'service_id', 'current_status_id', 'name', 'estimated_time'], 'required'],
            [['customer_id', 'user_id', 'service_id', 'current_status_id', 'priority'], 'integer'],
            [['placed_time', 'estimated_time', 'delivered_time'], 'safe'],
            [['rejected_reason', 'annotations'], 'string'],
            [['name'], 'string', 'max' => 120],
            [['current_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatuses::className(), 'targetAttribute' => ['current_status_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'service_id' => 'Service ID',
            'current_status_id' => 'Current Status ID',
            'name' => 'Name',
            'priority' => 'Priority',
            'placed_time' => 'Placed Time',
            'estimated_time' => 'Estimated Time',
            'delivered_time' => 'Delivered Time',
            'rejected_reason' => 'Rejected Reason',
            'annotations' => 'Annotations',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoices::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderHistories()
    {
        return $this->hasMany(OrderHistory::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentStatus()
    {
        return $this->hasOne(OrderStatuses::className(), ['id' => 'current_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
