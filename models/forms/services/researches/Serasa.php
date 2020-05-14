<?php 

namespace api\models\forms\services\researches;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class Serasa extends Pesquisa {
    public $tipo_pessoa;
    public $cpf;
    public $cnpj;

    public function rules()
    {
        return [
            [['tipo_pessoa'], 'required'],
            [['tipo_pessoa'], 'in', 'range' => [self::ENTIDADE_FISICA, self::ENTIDADE_JURIDICA]],
            [['cpf'], 'required', 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_FISICA;
            }],
            [['cnpj'], 'required', 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_JURIDICA;
            }],
            [['cnpj'], CnpjValidator::className(), 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_JURIDICA;
            }],
            [['cpf'], CpfValidator::className(), 'when' => function ($model) {
                return $model->tipo_pessoa == self::ENTIDADE_FISICA;
            }],
        ];
    }
}