<?php 

namespace api\models\forms\services\researches;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class Escritura extends Pesquisa {
    public $tipo_pessoa;
    public $cpf;
    public $cnpj;

    public function rules()
    {
        return [
            [['tipo_pessoa'], 'required'],
            [['cpf'], 'required', 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_FISICA;
            }],
            [['cnpj'], 'required', 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_JURIDICA;
            }],
            [['tipo_pessoa'], 'in', 'range' => [self::ENTIDADE_FISICA, self::ENTIDADE_JURIDICA]],
            [['cnpj'], CnpjValidator::className(), 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_JURIDICA;
            }],
            [['cpf'], CpfValidator::className(), 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_FISICA;
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_pessoa' => 'Tipo de pessoa',
            'cpf' => 'CPF',
            'cnpj' => 'CNPJ',
        ];
    }
}