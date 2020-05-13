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
            [['dados_imovel'], 'string']
        ];
    }

    public function attributeLabels()
    {
    }
}