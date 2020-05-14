<?php 

namespace api\models\forms\services\researches;

use yiibr\brvalidator\CnpjValidator;

class JuntaComercial extends Pesquisa {
    public $cnpj;
    public $razao_social;
    public $uf;
    public $observacoes;
    public $tipo;

    public function rules()
    {
        return [
            [['cnpj', 'uf', 'tipo'], 'required'],
            [['cnpj'], CnpjValidator::className()],
            [['razao_social', 'observacoes'], 'string'],
            [['uf'], 'string', 'max' => 2],
            [['tipo'], 'in', 'range' => [self::TIPO_INTEIRO_TEOR, self::TIPO_SIMPLIFICADA]],
        ];
    }
}