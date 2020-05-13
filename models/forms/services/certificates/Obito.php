<?php 

namespace api\models\forms\services\certificates;

class Obito extends Certidao {
    public $nome;
    public $nome_pai;
    public $nome_mae;
    public $data;
    public $livro;
    public $folha;
    public $termo;
    public $formato;
    public $tipo;
    public $apostilamento;
    public $qtde_xerox_autenticado;
    public $qtde_xerox_simples;
    public $reconhecimento_firma;
    public $traducao;

    public function rules()
    {
        return [
            [['nome', 'nome_mae', 'data', 'formato', 'tipo'], 'required'],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['livro', 'folha', 'termo'], 'integer'],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_ELETRONICA, self::FORMATO_FISICA_E_ELETRONICA]],
            [['tipo'], 'in', 'range' => [self::TIPO_INTEIRO_TEOR, self::TIPO_BREVE_RELATO]],
            [['nome', 'nome_pai', 'nome_mae'], 'string', 'max' => 255],
            [['traduzir_para'], 'in', 'range' => [self::TRADUCAO_ALEMAO, self::TRADUCAO_ESPANHOL, self::TRADUCAO_FRANCES, self::TRADUCAO_INGLES, self::TRADUCAO_ITALIANO]],
            [['apostilamento', 'reconhecimento_firma'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['qtde_xerox_autenticado', 'qtde_xerox_simples'], 'number', 'min' => 0, 'max' => 3]
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome',
            'nome_pai' => 'Nome do pai',
            'nome_mae' => 'Nome da mãe',
            'livro' => 'Livro',
            'folha' => 'Folha',
            'termo' => 'Termo',
            'formato' => 'Formato',
            'tipo' => 'Tipo',
            'traduzir_para' => 'Traduzir para',
            'apostilamento' => 'Apostilamento',
            'reconhecimento_firma' => 'Reconhecimento de firma',
            'qtde_xerox' => 'Quantidade de xerox',
            'qtde_xerox_simples' => 'Quantidade de xerox (simples)',
        ];
    }
}