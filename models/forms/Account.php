<?php

namespace api\models\forms;

use api\models\Customer;
use api\traits\SetErrorsAddingPrefix;
use yii\base\Model;
use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;
use api\models\User;

class Account extends Model
{
    use SetErrorsAddingPrefix;

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
    public $user_phone;
    public $user_is_admin;
    public $user_can_order_services;
    public $user_can_insert_credits;
    public $user_can_see_reports;
    public $user_can_see_invoices;

    public function rules()
    {
        return [
            [['customer_name', 'user_name', 'user_email', 'user_password'], 'required'],
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
            [['user_phone'], 'string', 'max' => 15],
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
            'user_phone' => 'Telefone',
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

        // remove password because contains sensitive information
        unset($fields['user_password']);

        return $fields;
    }

    public function save()
    {
        $this->user_is_admin = true;
        $this->user_can_insert_credits = true;
        $this->user_can_order_services = true;
        $this->user_can_see_invoices = true;
        $this->user_can_see_reports = true;

        if ($this->validate()) {
            $customer = new Customer;
            $user = new User;

            $customer->setAttributesWithPrefix($this->attributes, 'customer_');
            $user->setAttributesWithPrefix($this->attributes, 'user_');

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$customer->save()) {
                    throw new \Exception("Customer save failed");
                }

                $user->customer_id = $customer->id;
                if (!$user->save()) {
                    throw new \Exception("User save failed");
                }

                $transaction->commit();

                $this->user_id = $user->id;
                $this->customer_id = $customer->id;

                return true;
            } catch (\Exception $e) {
                $customer->validate();
                $user->validate();
                /**
                 * Besides Account being validate there can be still errors in models attributes that can't be validated easily here like user unique email
                 * if that happens convert the error from Customer/User to the corresponding Account attribute
                 */
                $this->setErrorsAddingPrefix($user->getErrors(), 'user_', ['customer_id']);
                $this->setErrorsAddingPrefix($customer->getErrors(), 'customer_');
                $transaction->rollback();
                return false;
            }
        }

        return false;
    }
}
