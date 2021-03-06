<?php

namespace api\models;

use api\traits\SetAttributesWithPrefix;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $last_login
 * @property int $is_admin
 * @property int $can_order_services
 * @property int $can_insert_credits
 * @property int $can_see_reports
 * @property int $can_see_invoices
 * @property int $customer_id
 *
 * @property Customer $customer
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    use SetAttributesWithPrefix;
    
    const SCENARIO_LOGIN = 'Login';

    const LOGIN_TOKEN_TIMEOUT_MINUTES = 600;
    const LOGIN_TOKEN_MAX_DURATION_MINUTES = 600;
    const PASSWORD_RESET_TOKEN_DURATION_MINUTES = 600;

    public static function tableName()
    {
        return 'users';
    }

    /**
     * Model validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'customer_id'], 'required'],
            [['last_login', 'last_api_request'], 'safe'],
            [['is_admin', 'can_order_services', 'can_insert_credits', 'can_see_reports', 'can_see_invoices'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['is_admin', 'can_order_services', 'can_insert_credits', 'can_see_reports', 'can_see_invoices'], 'default', 'value'=> true],
            [['customer_id'], 'integer'],
            [['phone'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 60],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['email'], 'string', 'max' => 200],
            [['password', 'access_token'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
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
            'name' => 'Nome',
            'email' => 'Email',
            'password' => 'Senha',
            'phone' => 'Telefone',
            'last_login' => 'Último login',
            'is_admin' => 'É administrador',
            'can_order_services' => 'Pode requisitar serviceos',
            'can_insert_credits' => 'Pode inserir créditos',
            'can_see_reports' => 'Pode visualizar relatórios',
            'can_see_invoices' => 'Pode ver faturas de pagamentos',
            'customer_id' => 'ID da empresa',
        ];
    }

    /**
     * Return an array of relations that can be expanded e.g. /user/5?expand=customer
     * 
     * @return array
     */
    public function extraFields()
    {
        return ['customer'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
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

        // remove field because contains sensitive information
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
                $customer = Customer::findOne($this->customer_id);
                if (count($customer->users) >= $customer->max_users) {
                    throw new ForbiddenHttpException("Máximo de usuários permitidos para essa empresa foi atingido. Usuários cadastrados: " . count($customer->users));
                }

                $this->password = password_hash($this->password, PASSWORD_BCRYPT);
                $this->access_token = Yii::$app->getSecurity()->generateRandomString();
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->refresh(); // refresh attributes with all database columns

        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeValidate()
    {
        $this->booleansToStrictBooleans();

        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->booleansToStrictBooleans();

        return parent::afterFind();
    }

    /**
     * Converts fields to strict booleans. As we always receive strings they are to strict boolean for validation and error message porposes
     */
    private function booleansToStrictBooleans()
    {
        $this->is_admin = boolval($this->is_admin);
        $this->can_insert_credits = boolval($this->can_insert_credits);
        $this->can_order_services = boolval($this->can_order_services);
        $this->can_see_invoices = boolval($this->can_see_invoices);
        $this->can_see_reports = boolval($this->can_see_reports);
        $this->deleted = boolval($this->deleted);
    }
}
