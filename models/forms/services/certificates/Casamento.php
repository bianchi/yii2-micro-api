<?php 

namespace api\models\forms\services\certificates;

class Nascimento extends Certidao {
    public $nome_conjuge_1;
    public $nome_conjuge_2;
    public $data;
    public $livro;
    public $folha;
    public $termo;
    public $formato;
    public $tipo;

    // Extras
    public $qtde_xerox_simples;
    public $qtde_xerox_autenticado;
    public $previa_digitalizada;
    public $reconhecimento_firma;
    public $apostilamento;
    public $pasta_protecao;
    public $traducao_juramentada;
    public $idioma_traducao_juramentada;
    public $aviso_recebimento;

    public $tipo_inteiro_teor;

    public function rules()
    {
        return [
            [['nome_conjuge_1', 'nome_conjuge_2','data', 'formato', 'tipo'], 'required'],
            [['nome_conjuge_1', 'nome_conjuge_2'], 'string', 'max' => 255],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['livro', 'folha', 'termo'], 'integer'],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_ELETRONICA, self::FORMATO_FISICA_E_ELETRONICA]],
            [['tipo'], 'in', 'range' => [self::TIPO_INTEIRO_TEOR, self::TIPO_BREVE_RELATO]],
            [['idioma_traducao_juramentada'], 'required', 'when' => function ($model) {
                return $model->traducao_juramentada;
            }],
            [['idioma_traducao_juramentada'], 'in', 'range' => [self::TRADUCAO_ALEMAO, self::TRADUCAO_ESPANHOL, self::TRADUCAO_FRANCES, self::TRADUCAO_INGLES, self::TRADUCAO_ITALIANO]],
            [['tipo_inteiro_teor'], 'required', 'when' => function ($model) {
                return $model->tipo == self::TIPO_INTEIRO_TEOR;
            }],
            [['idioma_traducao_juramentada'], 'in', 'range' => [self::TRADUCAO_ALEMAO, self::TRADUCAO_ESPANHOL, self::TRADUCAO_FRANCES, self::TRADUCAO_INGLES, self::TRADUCAO_ITALIANO]],
            [['apostilamento', 'previa_digitalizada', 'reconhecimento_firma', 'pasta_protecao', 'traducao_juramentada', 'aviso_recebimento'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['qtde_xerox_simples', 'qtde_xerox_autenticado'], 'number', 'min' => 0, 'max' => 3]
        ];
    }

    public function beforeValidate() 
    {
        $this->apostilamento = filter_var($this->apostilamento, FILTER_VALIDATE_BOOLEAN);
        $this->previa_digitalizada = filter_var($this->previa_digitalizada, FILTER_VALIDATE_BOOLEAN);
        $this->reconhecimento_firma = filter_var($this->reconhecimento_firma, FILTER_VALIDATE_BOOLEAN);
        $this->pasta_protecao = filter_var($this->pasta_protecao, FILTER_VALIDATE_BOOLEAN);
        $this->traducao_juramentada = filter_var($this->traducao_juramentada, FILTER_VALIDATE_BOOLEAN);
        $this->aviso_recebimento = filter_var($this->aviso_recebimento, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }
}