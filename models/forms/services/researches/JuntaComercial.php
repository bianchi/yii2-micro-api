<?php 

namespace api\models\forms\services\researches;

use yiibr\brvalidator\CnpjValidator;

class Nascimento extends Pesquisa {
    public $cnpj;
    public $tipo;
    public $qtde_xerox_simples;

    public function rules()
    {
        return [
            [['cnpj', 'tipo', 'qtde_xerox_simples'], 'required'],
            [['cnpj'], CnpjValidator::className()],
            [['tipo'], 'in', 'range' => [self::TIPO_INTEIRO_TEOR, self::TIPO_SIMPLIFICADA]],
            [['qtde_xerox_simples'], 'integer', 'min' => 0, 'max' => 3]
        ];
    }

    public function attributeLabels()
    {
        return [
            'cnpj' => 'CNPJ',
            'tipo' => 'Tipo',
            'qtde_xerox_simples' => 'Quantidade de xerox (simples)',
        ];
    }
}