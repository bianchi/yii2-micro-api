<?php 

namespace api\models\forms\services\certificates;

use yiibr\brvalidator\CpfValidator;

class Interdicao extends Certidao 
{
    public $nome;
    public $nome_mae;
    public $nome_pai;
    public $data_nascimento;
    public $uf_nascimento;
    public $cidade_nascimento;
    public $cpf;
    public $rg;
    public $ano_aproximado_ato;

    // Extras
    public $qtde_xerox_simples;
    public $qtde_xerox_autenticado;
    public $previa_digitalizada;
    public $apostilamento;
    public $pasta_protecao;

    public function rules()
    {
        return [
            [['nome', 'nome_mae', 'data_nascimento', 'uf_nascimento', 'cidade_nascimento', 'cpf', 'rg'], 'required'],
            [['nome', 'nome_pai', 'nome_mae'], 'string', 'max' => 255],
            [['data_nascimento'], 'date', 'format' => 'php:Y-m-d'],
            [['uf_nascimento'], 'string', 'max' => 2],
            [['cidade_nascimento'], 'string', 'max' => 255],
            [['cpf'], CpfValidator::className()],
            [['rg'], 'string', 'max' => 20],
            [['ano_aproximado_ato'], 'integer', 'min' => 1500, 'max' => date('Y')],
            [['apostilamento', 'previa_digitalizada', 'pasta_protecao'], 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true],
            [['qtde_xerox_simples', 'qtde_xerox_autenticado'], 'number', 'min' => 0, 'max' => 3]
        ];
    }

    public function beforeValidate() 
    {
        $this->apostilamento = filter_var($this->apostilamento, FILTER_VALIDATE_BOOLEAN);
        $this->previa_digitalizada = filter_var($this->previa_digitalizada, FILTER_VALIDATE_BOOLEAN);
        $this->pasta_protecao = filter_var($this->pasta_protecao, FILTER_VALIDATE_BOOLEAN);

        return parent::beforeValidate();
    }
}