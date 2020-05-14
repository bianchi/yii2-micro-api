<?php 

namespace api\models\forms\services\certificates;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class NegativaDebitosTributosFederaisCNDTNIDA extends Certidao 
{
    public $tipo_pessoa;
    public $cpf;
    public $cnpj;
    public $formato;
    public $pasta_protecao;

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