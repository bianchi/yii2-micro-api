<?php

namespace api\models;

use app\models\FinancialTransaction;
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
            [['name', 'document_number', 'zip_code', 'public_place', 'number', 'entity_type'], 'required'],
            [['entity_type'], 'string'],
            [['max_users'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['document_number'], 'string', 'max' => 14],
            [['zip_code', 'number'], 'string', 'max' => 8],
            [['public_place', 'key', 'secret'], 'string', 'max' => 120],
            [['complement'], 'string', 'max' => 60],
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
        return ['account_balance' => 'accountBalance'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinancialTransactions()
    {
        return $this->hasMany(FinancialTransaction::className(), ['customer_id' => 'id']);
    }

    public function getAccountBalance()
    {
        $totalCredits = FinancialTransaction::find()->where(['customer_id' => $this->id, 'operation' => FinancialTransaction::OPERATION_CREDIT])->sum('amount');
        $totalDedits = FinancialTransaction::find()->where(['customer_id' => $this->id, 'operation' => FinancialTransaction::OPERATION_DEBIT])->sum('amount');

        return bcsub($totalCredits, $totalDedits, 2);
    }
}
