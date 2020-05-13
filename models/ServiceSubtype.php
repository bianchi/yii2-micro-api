<?php

namespace app\models;

use api\models\Service;
use Yii;

/**
 * This is the model class for table "services_subtypes".
 *
 * @property int $id
 * @property int $service_id
 * @property string $name
 * @property string $backoffice_url
 *
 * @property Services $service
 */
class ServiceSubtype extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services_subtypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'name', 'backoffice_url'], 'required'],
            [['service_id'], 'integer'],
            [['name', 'backoffice_url'], 'string', 'max' => 120],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'name' => 'Name',
            'backoffice_url' => 'Backoffice Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
