<?php 

namespace api\models\forms\services\certificates;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class NegativaFGTS extends Certidao 
{
    public $cnpj;
    public $cei;
    public $uf;
    public $formato;
    public $pasta_protecao;

    public function rules()
    {
        return [
            [['cnpj', 'cei', 'uf', 'formato'], 'required'],
            [['cnpj'], CnpjValidator::className()],
            [['cei'], 'string', 'max' => 13],
            [['uf'], 'string', 'max' => 2],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_ELETRONICA]],
            [['pasta_protecao'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true]
        ];
    }

    public function beforeValidate() 
    {
        $this->pasta_protecao = filter_var($this->pasta_protecao, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }
}