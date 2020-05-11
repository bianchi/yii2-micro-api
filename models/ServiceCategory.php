<?php

namespace app\models;

use api\models\Service;
use Yii;

/**
 * This is the model class for table "services_categories".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 *
 * @property Services[] $services
 */
class ServiceCategory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'services_categories';
    }

    public function rules()
    {
        return [
            [['code'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 120],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ];
    }

    public function extraFields()
    {
        return ['services'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['category_id' => 'id']);
    }
}
