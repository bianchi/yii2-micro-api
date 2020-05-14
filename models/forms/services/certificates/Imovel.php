<?php 

namespace api\models\forms\services\certificates;

class Imovel extends Certidao 
{
    const TIPO_MATRICULA = 'matricula';
    const TIPO_VINTENARIA = 'vintenaria';
    const TIPO_INTEIRO_TEOR_ONUS_ACAO = 'inteiroteoronusacao';
    const TIPO_TRANSCRICAO = 'transcricao';

    public $tipo;
    public $subtipo;
    public $matricula;
    public $transcricao;
    public $data_emissao;
    public $livro;
    public $dados_imovel;

    // Extras
    public $qtde_xerox_simples;
    public $previa_digitalizada;
    public $apostilamento;
    public $pasta_protecao;
    public $aviso_recebimento;
    
    public function rules()
    {
        return [
            [['matricula'], 'required', 'when' => function($model) {
                return $model->tipo == self::TIPO_MATRICULA || $model->subtipo == self::TIPO_MATRICULA ;
            }],
            [['transcricao', 'data_emissao'], 'required', 'when' => function($model) {
                return $model->tipo == self::TIPO_TRANSCRICAO || $model->subtipo == self::TIPO_TRANSCRICAO ;
            }],
            [['matricula', 'transcricao', 'livro'], 'integer'],
            [['data_emissao'], 'date', 'format' => 'php:Y-m-d'],
            [['tipo'], 'in', 'range' => [self::TIPO_MATRICULA, self::TIPO_VINTENARIA, self::TIPO_INTEIRO_TEOR_ONUS_ACAO, self::TIPO_TRANSCRICAO]],
            [['subtipo'], 'in', 'range' => [self::TIPO_MATRICULA, self::TIPO_TRANSCRICAO]],
            [['subtipo'], 'required', 'when' => function($model) {
                return in_array($model->tipo, [self::TIPO_VINTENARIA, self::TIPO_INTEIRO_TEOR_ONUS_ACAO]);
            }],
            [['dados_imovel'], 'string'],
            [['apostilamento', 'previa_digitalizada', 'pasta_protecao', 'aviso_recebimento'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['qtde_xerox_simples'], 'number', 'min' => 0, 'max' => 3]
        ];
    }

    public function beforeValidate() 
    {
        $this->apostilamento = filter_var($this->apostilamento, FILTER_VALIDATE_BOOLEAN);
        $this->previa_digitalizada = filter_var($this->previa_digitalizada, FILTER_VALIDATE_BOOLEAN);
        $this->pasta_protecao = filter_var($this->pasta_protecao, FILTER_VALIDATE_BOOLEAN);
        $this->aviso_recebimento = filter_var($this->aviso_recebimento, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }
}