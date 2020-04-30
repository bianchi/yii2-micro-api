<?php

namespace api\models\forms;

/**
 *
 * @property string $number
 * @property string $holder_name
 * @property string $due_date
 * @property string $cvv
 * @property string $cpf_cnpj
 *
 * @property Users[] $users
 */
class CreditCard extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
            [['key', 'secret'], 'string', 'max' => 120],
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
}
