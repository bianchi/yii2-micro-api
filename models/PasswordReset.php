<?php

namespace api\models;

use Yii;
use api\models\User;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "password_reset".
 *
 * @property int $user_id
 * @property string $token
 * @property string $expiration_time
 * @property int $already_used
 *
 * @property Users $user
 */
class PasswordReset extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'password_reset';
    }

    public function rules()
    {
        return [
            [['token', 'user_id', 'expiration_time'], 'required'],
            [['user_id'], 'integer'],
            [['already_used'], 'boolean'],
            [['expiration_time'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['token'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'token' => 'Token',
            'expiration_time' => 'Expiration Time',
            'already_used' => 'Already Used',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['valid'] = 'isValid';

        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterFind()
    {
        $this->already_used = boolval($this->already_used);

        return parent::afterFind();
    }

    public function getIsValid()
    {
        try {
            $this->checkValid();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkValid()
    {
        $currentTime = new \Datetime;
        $tokenExpiration = new \Datetime($this->expiration_time);
        if ($tokenExpiration < $currentTime) {
            throw new NotAcceptableHttpException("Token has expired");
        }

        if ($this->already_used) {
            throw new NotAcceptableHttpException("Token already used");
        }

        return true;
    }
}
