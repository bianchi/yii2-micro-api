<?php

namespace api\models\forms;

use api\models\backoffice\Api;
use api\models\Invoice;
use api\traits\SetAttributesWithPrefix;
use yii\base\Model;
use api\models\User;
use api\traits\SetErrorsAddingPrefix;
use yii2mod\validators\ECCValidator;

class WalletDeposit extends Model
{
    use SetAttributesWithPrefix, SetErrorsAddingPrefix;
    
    public $invoice_id;
    public $invoice_payment_method;
    public $invoice_amount;
    public $invoice_placed_time;
    public $invoice_approved_time;
    public $credit_card_holder_name;
    public $credit_card_number;
    public $credit_card_document_number;
    public $credit_card_due_date;
    public $credit_card_cvv;

    public function rules()
    {
        return [
            [['invoice_payment_method', 'invoice_amount', 'credit_card_number', 'credit_card_holder_name', 'credit_card_document_number', 'credit_card_due_date', 'credit_card_cvv'], 'required'],
            [['invoice_payment_method'], 'in', 'range' => [Invoice::PAYMENT_METHOD_CREDIT_CARD, Invoice::PAYMENT_METHOD_BOLETO]],
            [['invoice_amount'], 'number'],
            [['credit_card_number'], ECCValidator::className(), 'message' => 'Número do cartão inválido'],
            [['credit_card_number'], 'integer'],
            [['credit_card_number'], 'string', 'max' => 20],
            [['credit_card_holder_name'], 'string', 'max' => 120],
            [['credit_card_document_number'], 'string', 'max' => 15],
            [['credit_card_cvv'], 'string', 'max' => 4],
            [['credit_card_due_date'], 'date', 'format' => 'y-m']
        ];
    }

    public function attributeLabels()
    {
        return [
            'credit_card_number' => 'Número do cartão',
            'credit_card_holder_name' => 'Nome do titular',
            'credit_card_cvv' => 'credit_card_cvv',
            'credit_card_document_number' => 'CPF/CNPJ',
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $loggedUser = User::findOne(\Yii::$app->user->id);
            
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $invoice = new Invoice;
                $invoice->setAttributesWithPrefix($this->attributes, 'invoice_');
                $invoice->operation = Invoice::OPERATION_CREDIT;
                $invoice->user_id = $loggedUser->id;
                $invoice->customer_id = $loggedUser->customer_id;
                $invoice->placed_time = date('Y-m-d H:i:s');

                if (!$invoice->save()) {
                    throw new \Exception("Invoice save failed");
                }

                $this->invoice_id = $invoice->id;

                $backofficeApi = new Api($loggedUser->customer->backoffice_email, $loggedUser->customer->backoffice_password);
                $response = $backofficeApi->insertCredits();

                if ($response == true) {
                    if ($this->invoice_payment_method == Invoice::PAYMENT_METHOD_CREDIT_CARD) {
                        $invoice->approved_time = date('Y-m-d H:i:s'); // TODO pegar da transação do backOffice
                        $invoice->updateAttributes(['approved_time']);
                    } elseif ($this->invoice_payment_method == Invoice::PAYMENT_METHOD_BOLETO) {
                        // grava a informação relacionando com algo do backoffice
                    }

                    $this->invoice_placed_time = $invoice->placed_time;
                    $this->invoice_approved_time = $invoice->approved_time;

                    $transaction->commit();
                    return true;
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->setErrorsAddingPrefix($invoice->getErrors(), 'invoice_');
                return false;
            }
        }

        return false;
    }
}
