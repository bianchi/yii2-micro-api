<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 *
 * @property Orders[] $orders
 */
class Service extends \yii\db\ActiveRecord
{
    const TYPE_RESEARCH = 'Research';
    const TYPE_CERTIFICATE = 'Certificate';

    public static function tableName()
    {
        return 'services';
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
            [['type'], 'in', 'range' => [self::TYPE_RESEARCH, self::TYPE_CERTIFICATE]],
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
        return $this->hasMany(Order::className(), ['service_id' => 'id']);
    }

    public function getFullname()
    {
        if ($this->type == self::TYPE_CERTIFICATE) {
            return 'Certidão de ' . $this->name;
        } elseif ($this->type == self::TYPE_RESEARCH) {
            return 'Pesquisa de ' . $this->name;
        }

        return $this->name;
    }
}
