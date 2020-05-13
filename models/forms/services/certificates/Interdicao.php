<?php 

namespace api\models\forms\services\certificates;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class Escritura extends Certidao {
    public $nome;
    public $nome_mae;
    public $nome_pai;
    public $data_nascimento;
    public $uf_nascimento;
    public $cidade_nascimento;
    public $cpf;
    public $rg;
    public $ano_aproximado_ato;
    public $qtde_xerox_autenticado;
    public $qtde_xerox_simples;
    public $formato;
    public $apostilamento;
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
            [['nome', 'nome_mae','data','formato', 'tipo'], 'required'],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['livro', 'folha', 'termo'], 'integer'],
            [['formato'], 'in', 'range' => [self::FORMATO_FISICA, self::FORMATO_FISICA_E_ELETRONICA]],
            [['apostilamento', 'pasta_protecao'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome completo registrado',
            'nome_pai' => 'Nome completo do pai',
            'nome_mae' => 'Nome completo da mãe',
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