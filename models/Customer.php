<?php

namespace api\models;

use api\models\Invoice;
use api\traits\SetAttributesWithPrefix;
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
    use SetAttributesWithPrefix;
    
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
            [['address_neighborhood'], 'string', 'max' => 80],
            [['address_public_place', 'address_city'], 'string', 'max' => 120],
            [['address_uf'], 'string', 'max' => 2],
            [['address_complement'], 'string', 'max' => 60],
            [['entity_type'], 'in', 'range' => [self::ENTITY_TYPE_PF, self::ENTITY_TYPE_PJ]],
            [['document_number'], CpfValidator::className(), 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PF;
            }],
            [['document_number'], CnpjValidator::className(), 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PJ;
            }],
            [['document_number'], 'unique', 'message' => 'O CNPJ digitado já foi utilizado', 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PJ;
            }],
            [['document_number'], 'unique', 'message' => 'O CPF digitado já foi utilizado', 'when' => function($model) {
                return $model->entity_type == self::ENTITY_TYPE_PF;
            }],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'corporate_name' => 'Razão social',
            'entity_type' => 'Entidade fiscal',
            'document_number' => 'CPF/CNPJ',
            'address_zip_code' => 'CEP',
            'address_public_place' => 'Logradouro',
            'address_number' => 'Número',
            'address_complement' => 'Complemento',
            'address_neighborhood' => 'Bairro',
            'address_city' => 'Cidade',
            'address_uf' => 'UF',
            'key' => 'Key',
            'secret' => 'Secret',
            'max_users' => 'Max Users',
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
