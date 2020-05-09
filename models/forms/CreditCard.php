<?php

namespace api\models\forms;

/**
 *
 * @property string $number
 * @property string $holder_name
 * @property string $due_date
 * @property string $cvv
 * @property string $service_number
 *
 */
class CreditCard extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['number'], 'integer'],
            [['holder_name'], 'string', 'max' => 120],
            [['service_number'], 'string', 'max' => 15],
            [['cvv'], 'string', 'max' => 4],
            [['due_date'], 'date']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'number' => 'Número do cartão',
            'holder_name' => 'Nome do titular',
            'cvv' => 'CVV',
            'service_number' => 'CPF ou CNPJ',
        ];
    }
}
