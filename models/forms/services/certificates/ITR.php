<?php 

namespace api\models\forms\services\certificates;

class ITR extends Certidao 
{
    public $nirf;
    public $formato;

    // Extras
    public $pasta_protecao;

    public function rules()
    {
        return [
            [['nirf'], 'required'],
            [['nirf'], 'string', 'length' => 8],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_ELETRONICA]],
            [['pasta_protecao'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
        ];
    }

    public function beforeValidate() 
    {
        $this->pasta_protecao = filter_var($this->pasta_protecao, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }
}