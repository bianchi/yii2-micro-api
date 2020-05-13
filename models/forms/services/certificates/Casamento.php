<?php 

namespace api\models\forms\services\certificates;

class Nascimento extends Certidao {
    public $nome_conjuge_1;
    public $nome_conjuge_2;
    public $livro;
    public $folha;
    public $termo;
    public $formato;
    public $tipo;
    public $apostilamento;
    public $qtde_xerox;
    public $qtde_xerox_simples;
    public $reconhecimento_firma;
    public $traducao;

    public function rules()
    {
        return [
            [['nome_conjuge_1', 'nome_conjuge_2', 'formato', 'tipo'], 'required'],
            [['livro', 'folha', 'termo'], 'integer'],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_ELETRONICA]],
            [['tipo'], 'in', 'range' => [self::TIPO_INTEIRO_TEOR, self::TIPO_BREVE_RELATO]],
            [['nome_conjuge_1', 'nome_conjuge_2'], 'string', 'max' => 255],
            [['traduzir_para'], 'in', 'range' => [self::TRADUCAO_ALEMAO, self::TRADUCAO_ESPANHOL, self::TRADUCAO_FRANCES, self::TRADUCAO_INGLES, self::TRADUCAO_ITALIANO]],
            [['apostilamento', 'reconhecimento_firma'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['qtde_xerox', 'qtde_xerox_simples'], 'integer', 'min' => 0, 'max' => 3]
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome_conjuge_1' => 'Cônjuge 1',
            'nome_conjuge_2' => 'Cônjuge 2',
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