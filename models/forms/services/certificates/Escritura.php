<?php 

namespace api\models\forms\services\certificates;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class Escritura extends Certidao {
    public $tipo_pessoa;
    public $cpf;
    public $cnpj;
    public $livro;
    public $folha;
    public $formato;

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
            [['nome', 'nome_mae','data','formato', 'tipo'], 'required'],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['livro', 'folha', 'termo'], 'integer'],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA]],
        ];
    }
}