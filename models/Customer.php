<?php

namespace api\models;

use app\models\Invoice;
use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

/**
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $secret
 *
 * @property Users[] $users
 */
class Customer extends \yii\db\ActiveRecord
{
    const ENTITY_TYPE_PF = 'PF';
    const ENTITY_TYPE_PJ = 'PJ';
    
    public static function tableName()
    {
        return 'customers';
    }
    
    public function rules()
    {
        return [
            [['name', 'document_number', 'entity_type'], 'required'],
            [['entity_type'], 'string'],
            [['max_users'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['document_number'], 'string', 'max' => 14],
            [['address_zip_code', 'address_number'], 'string', 'max' => 8],
            [['address_public_place', 'key', 'secret'], 'string', 'max' => 120],
            [['address_complement'], 'string', 'max' => 60],
            [['document_number'], CpfValidator::className(), 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PF;
            }],
            [['document_number'], CnpjValidator::className(), 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PJ;
            }]
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'key' => 'Key',
            'secret' => 'Secret',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['customer_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['key'], $fields['secret']);

        return $fields;
    }

    public function extraFields()
    {
        return [
            'account_balance' => 'accountBalance',
            'total_orders' => 'totalOrders',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['customer_id' => 'id']);
    }

    public function getInvoiceCreditsBetweenDates($beginDate, $endDate)
    {
        return Invoice::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['operation' => Invoice::OPERATION_CREDIT])
            ->andWhere(['between', 'placed_time', $beginDate, $endDate])
            ->sum('amount');
    }

    public function getInvoiceDebitsBetweenDates($beginDate, $endDate)
    {
        return Invoice::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['operation' => Invoice::OPERATION_DEBIT])
            ->andWhere(['between', 'placed_time', $beginDate, $endDate])
            ->sum('amount');
    }

    public function getAccountBalance()
    {
        $totalCredits = Invoice::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['operation' => Invoice::OPERATION_CREDIT])
            ->andWhere(['IS NOT', 'approved_time', null])
            ->sum('amount');


        $totalDebits = Invoice::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['operation' => Invoice::OPERATION_DEBIT])
            ->andWhere(['IS NOT', 'approved_time', null])
            ->sum('amount');

        return bcsub($totalCredits, $totalDebits, 2);
    }

    public function getTotalOrders()
    {
        return Order::find()->joinWith(['user u'])->where(['u.customer_id' => $this->id])->count();
    }
}
