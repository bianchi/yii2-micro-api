<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $last_login
 * @property int $is_admin
 * @property int $can_order_document
 * @property int $can_insert_credits
 * @property int $can_see_reports
 * @property int $can_see_billing
 * @property int $company_id
 *
 * @property Company $company
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const SCENARIO_LOGIN = 'Login';

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'email', 'password', 'company_id'], 'required'],
            [['last_login', 'last_api_request'], 'safe'],
            [['is_admin', 'can_order_document', 'can_insert_credits', 'can_see_reports', 'can_see_billing'], 'boolean'],
            [['company_id'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['email'], 'string', 'max' => 200],
            [['password', 'access_token'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'email' => 'Email',
            'password' => 'Senha',
            'last_login' => 'Último login',
            'is_admin' => 'É administrador',
            'can_order_document' => 'Pode requisitar documentos',
            'can_insert_credits' => 'Pode inserir créditos',
            'can_see_reports' => 'Pode visualizar relatórios',
            'can_see_billing' => 'Pode ver faturas de pagamentos',
            'company_id' => 'ID da empresa',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

     /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['password']);

        // access_token is only sent in /users/login
        if ($this->scenario != self::SCENARIO_LOGIN) {
            unset($fields['access_token']);
        }

        return $fields;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = password_hash($this->password, PASSWORD_BCRYPT);
                $this->access_token = Yii::$app->getSecurity()->generateRandomString();
            }
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->refresh();

        return parent::afterSave($insert, $changedAttributes);
    }
}
