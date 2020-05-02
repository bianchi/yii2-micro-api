<?php

namespace app\models;

use api\models\Customer;
use api\models\Order;
use api\models\User;
use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property int $customer_id
 * @property int $user_id
 * @property int $order_id
 * @property string $operation
 * @property double $amount
 *
 * @property Customers $customer
 * @property Orders $order
 * @property Users $user
 */
class Invoice extends \yii\db\ActiveRecord
{
    const OPERATION_CREDIT = 'C';
    const OPERATION_DEBIT = 'D';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'user_id', 'operation', 'amount'], 'required'],
            [['customer_id', 'user_id', 'order_id'], 'integer'],
            [['operation'], 'string'],
            [['amount'], 'number'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'operation' => 'Operation',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
