<?php

namespace api\models;

/**
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $secret
 *
 * @property Users[] $users
 */
class Company extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'companies';
    }
    
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

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['company_id' => 'id']);
    }
}
