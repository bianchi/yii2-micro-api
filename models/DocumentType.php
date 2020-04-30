<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "document_types".
 *
 * @property int $id
 * @property string $name
 *
 * @property Orders[] $orders
 */
class DocumentType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'document_types';
    }

    /**
     * Model validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 120],
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
            'name' => 'Name',
        ];
    }

    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['document_type_id' => 'id']);
    }
}
