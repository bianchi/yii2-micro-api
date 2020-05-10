<?php

namespace api\models\forms;

use api\models\Invoice;
use api\traits\SetAttributesWithPrefix;
use yii\base\Model;
use api\models\User;

class WalletDeposit extends Model
{
    use SetAttributesWithPrefix;
    
    public $invoice_id;
    public $invoice_payment_method;
    public $credit_card_holder_name;
    public $credit_card_number;
    public $credit_card_document_number;
    public $credit_card_due_date;
    public $credit_card_cvv;

    public function rules()
    {
        return [
            [['credit_card_number', 'credit_card_holder_name', 'credit_card_document_number', 'credit_card_due_date', 'credit_card_cvv'], 'required'],
            [['invoice_payment_method'], 'in', 'range' => [Invoice::PAYMENT_METHOD_CREDIT_CARD, Invoice::PAYMENT_METHOD_BOLETO]],
            [['credit_card_number'], 'integer'],
            [['credit_card_number'], 'string', 'max' => 16],
            [['credit_card_holder_name'], 'string', 'max' => 120],
            [['credit_card_document_number'], 'string', 'max' => 15],
            [['credit_card_cvv'], 'string', 'max' => 4],
            [['credit_card_due_date'], 'date', 'format' => 'Y-m']
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

            if ($this->invoice_payment_method == Invoice::PAYMENT_METHOD_CREDIT_CARD) {
                $creditCard = new CreditCard;
                $creditCard->setAttributesWithPrefix(\Yii::$app->getRequest()->getBodyParams(), 'credit_card_');

                if ($creditCard->validate()) {
                    $transactionApproved = true; // chama o backoffice

                    if ($transactionApproved) {
                        $invoice = new Invoice;
                        $invoice->setAttributesWithPrefix($this->attributes, 'invoice_');
                        $invoice->operation = Invoice::OPERATION_CREDIT;
                        $invoice->user_id = $loggedUser->id;
                        $invoice->customer_id = $loggedUser->customer_id;
                        $invoice->placed_time = date('Y-m-d H:i:s');
                        $invoice->approved_time = date('Y-m-d H:i:s'); // TODO pegar da transação do backOffice
                    }
                } else {

                }
            }

            $invoice = new Invoice;
            $invoice->setAttributesWithPrefix($this->attributes, 'invoice_');
            $invoice->operation = Invoice::OPERATION_CREDIT;
            $invoice->user_id = $loggedUser->id;
            $invoice->customer_id = $loggedUser->customer_id;
            $invoice->placed_time = date('Y-m-d H:i:s');

            if (!$invoice->save()) {
                throw new \Exception("Invoice save failed");
            }
        }

        return false;
    }
}
