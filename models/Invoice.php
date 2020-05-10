<?php

namespace api\models;

use api\models\Customer;
use api\models\Order;
use api\models\User;
use api\traits\SetAttributesWithPrefix;
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
    use SetAttributesWithPrefix;
    
    const SCENARIO_INSERT_CREDITS = 'insert-credits';

    const OPERATION_CREDIT = 'C';
    const OPERATION_DEBIT = 'D';

    const PAYMENT_METHOD_CREDIT_CARD = 'CC';
    const PAYMENT_METHOD_BOLETO = 'BO';

    public static function tableName()
    {
        return 'invoices';
    }

    public function rules()
    {
        return [
            [['customer_id', 'user_id', 'operation', 'amount'], 'required'],
            [['customer_id', 'user_id', 'order_id'], 'integer'],
            [['operation'], 'string'],
            [['amount'], 'number'],
            [['placed_time', 'approved_time'], 'date', 'format' => 'php: Y-m-d H:i:s'],
            [['payment_method'], 'in', 'range' => [self::PAYMENT_METHOD_CREDIT_CARD, self::PAYMENT_METHOD_BOLETO]],
            [['payment_method'], 'required', 'on' => self::SCENARIO_INSERT_CREDITS],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_INSERT_CREDITS] = ['customer_id', 'user_id', 'operation', 'amount', 'payment_method', 'placed_time'];

        return $scenarios;
    }

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
