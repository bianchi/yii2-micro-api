<?php

namespace api\models\forms;

use api\models\Customer;
use yii\base\Model;
use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;
use api\models\User;

class Account extends Model
{
    public $customer_id;
    public $customer_entity_type;
    public $customer_name;
    public $customer_document_number;
    public $customer_address_zip_code;
    public $customer_address_number;
    public $customer_address_public_place;
    public $customer_address_complement;
    public $customer_address_neighborhood;
    public $customer_address_city;
    public $customer_address_uf;

    public $user_id;
    public $user_name;
    public $user_email;
    public $user_password;
    public $user_is_admin;
    public $user_can_order_document;
    public $user_can_insert_credits;
    public $user_can_see_reports;
    public $user_can_see_invoices;

    public $customer;
    public $user;

    public function rules()
    {
        return [
            [['customer_entity_type', 'customer_name', 'user_name', 'user_email', 'user_password'], 'required'],
            [['customer_name'], 'string', 'max' => 100],
            [['customer_document_number'], 'string', 'max' => 14],
            [['customer_address_zip_code', 'customer_address_number'], 'string', 'max' => 8],
            [['customer_address_neighborhood'], 'string', 'max' => 80],
            [['customer_address_public_place', 'customer_address_city'], 'string', 'max' => 120],
            [['customer_address_uf'], 'string', 'max' => 2],
            [['customer_address_complement'], 'string', 'max' => 60],
            [['customer_document_number'], CpfValidator::className(), 'when' => function($model) {
                return $model->customer_entity_type == Customer::ENTITY_TYPE_PF;
            }],
            [['customer_document_number'], CnpjValidator::className(), 'when' => function($model) {
                return $model->customer_entity_type == Customer::ENTITY_TYPE_PJ;
            }],
            [['user_name'], 'string', 'max' => 60],
            [['user_email'], 'email'],
            [['user_email'], 'string', 'max' => 200],
            [['user_password'], 'string', 'max' => 255],
            [['customer_entity_type'], 'in', 'range' => [Customer::ENTITY_TYPE_PF, Customer::ENTITY_TYPE_PJ]]

        ];
    }
    
    public function attributeLabels()
    {
        return [
            'customer_name' => 'Nome do cliente',
            'customer_entity_type' => 'Tipo de entidade fiscal',
            'customer_address_zip_code' => 'CEP',
            'customer_address_public_place' => 'Logradouro',
            'customer_address_number' => 'Número',
            'customer_address_complement' => 'Complemento',
            'customer_document_number' => 'CPF/CNPJ',
            'customer_address_neighborhood' => 'Bairro',
            'customer_address_city' => 'Cidade',
            'customer_address_uf' => 'UF',
            'user_name' => 'Nome do usuário',
            'user_email' => 'Email',
            'user_password' => 'Senha',
        ];
    }

    public function beforeValidate()
    {
        if ($this->customer_entity_type == Customer::ENTITY_TYPE_PF && empty($this->customer_name)) {
            $this->customer_name = $this->user_name;
        }

        return parent::beforeValidate();
    }

    public function fields()
    {
        $fields = parent::fields();

        // unset this objects because we don't want to send then in the request
        unset($fields['customer'], $fields['user']);

        // remove password because contains sensitive information
        unset($fields['user_password']);

        return $fields;
    }

    public function save()
    {
        if ($this->validate()) {
            $customer = new Customer;
            $user = new User;

            // set values for customer and user models
            foreach ($this->attributes as $key => $value) {
                // ignored because it will hold saved customer, user, customer_id and user_id respectively if everything goes fine
                if (!in_array($key, ['customer', 'user', 'customer_id', 'user_id'])) {
                    // if current attribute has "customer" remove the "customer" part to set real attribute value in Customer model.
                    // e.g. Account->customer_name goes to Customer->name
                    if (stripos($key, 'customer') !== false) {
                        $attribute = str_replace('customer_', '', $key);
                        $customer->setAttribute($attribute, $value);
                    }

                    // if current attribute has "user" remove the "user" part to set real attribute value in User model.
                    // e.g. Account->user_name goes to User->name
                    if (stripos($key, 'user') !== false) {
                        $attribute = str_replace('user_', '', $key);
                        $user->setAttribute($attribute, $value);
                    }
                }
            }

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$customer->save()) {
                    throw new \Exception("Customer save failed");
                }

                $user->customer_id = $customer->id;
                $user->is_admin = true;
                $user->can_insert_credits = true;
                $user->can_order_document = true;
                $user->can_see_invoices = true;
                $user->can_see_reports = true;
                if (!$user->save()) {
                    throw new \Exception("User save failed");
                }

                $transaction->commit();

                $this->user_id = $user->id;
                $this->customer_id = $customer->id;

                return true;
            } catch (\Exception $e) {
                $this->convertErrorsToAccount($customer, $user);
                $transaction->rollback();
                return false;
            }
        }

        return false;
    }

    /**
     * Besides Account being validate there can be still errors in models attributes that can't be validated easily here like user unique email
     * if that happens convert the error from Customer/User to the corresponding Account attribute
     */
    private function convertErrorsToAccount($customer, $user)
    {
        $accountErrors = [];
        // Adds user_ to the attribute errors. E.g. User->email errors goes to Account->user_email
        foreach ($user->getErrors() as $attribute => $errors) {
            $accountErrors['user_' . $attribute] = $errors;
        }

        // Adds customer_ to the attribute errors. E.g. Customer->name errors goes to Account->customer_name
        foreach ($customer->getErrors() as $attribute => $errors) {
            $accountErrors['customer_' . $attribute] = $errors;
        }

        $this->addErrors($accountErrors);
    }
}
