<?php

namespace api\models\forms;

use api\traits\SetAttributesWithPrefix;
use yii\base\Model;

/**
 *
 * @property string $number
 * @property string $holder_name
 * @property string $due_date
 * @property string $cvv
 * @property string $document_number
 *
 */
class CreditCard extends Model
{
    use SetAttributesWithPrefix;
    
    public $holder_name;
    public $number;
    public $document_number;
    public $due_date;
    public $cvv;

    public function rules()
    {
        return [
            [['number', 'holder_name', 'document_number', 'due_date', 'cvv'], 'required'],
            [['number'], 'integer'],
            [['number'], 'string', 'max' => 16],
            [['holder_name'], 'string', 'max' => 120],
            [['document_number'], 'string', 'max' => 15],
            [['cvv'], 'string', 'max' => 4],
            [['due_date'], 'date', 'format' => 'Y-m']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'number' => 'Número do cartão',
            'holder_name' => 'Nome do titular',
            'cvv' => 'CVV',
            'document_number' => 'CPF/CNPJ',
        ];
    }
}
