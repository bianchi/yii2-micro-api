<?php 

namespace api\models\forms\services\certificates;

use yii\base\Model;

class Certidao extends Model 
{
    const FORMATO_FISICA = 'fisica';
    const FORMATO_ELETRONICA = 'eletronica';
    const FORMATO_FISICA_E_ELETRONICA = 'fisicaeletronica';

    const TIPO_INTEIRO_TEOR = 'inteiroteor';
    const TIPO_BREVE_RELATO = 'breverelato';

    const TRADUCAO_ALEMAO = 'alemao';
    const TRADUCAO_ITALIANO = 'italiano';
    const TRADUCAO_ESPANHOL = 'espanhol';
    const TRADUCAO_INGLES = 'ingles';
    const TRADUCAO_FRANCES = 'frances';

    const ENTIDADE_FISICA = 'fisica';
    const ENTIDADE_JURIDICA = 'juridica';

    const INTEIRO_TEOR_TRANSCRICAO_DIGITAL = 'transcricaodigital';
    const INTEIRO_TEOR_REPROGRAFICA = 'reprografica';
    const INTEIRO_TEOR_TRANSCRICAO_DIGITAL_E_REPROGRAFICA = 'transcricaodigitalreprografica';

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome completo registrado',
            'nome_pai' => 'Nome completo do pai',
            'nome_mae' => 'Nome completo da mãe',
            'nome_conjuge_1' => 'Cônjuge 1',
            'nome_conjuge_2' => 'Cônjuge 2',
            'data_nascimento' => 'Data de nascimento',
            'uf_nascimento' => 'Estado de nascimento',
            'cidade_nascimento' => 'Cidade de nascimento',
            'cpf' => 'CPF',
            'rg' => 'RG',
            'livro' => 'Livro',
            'folha' => 'Folha',
            'termo' => 'Termo',
            'formato' => 'Formato',
            'ano_aproximado_ato' => 'Ano aproximado do ato',
            'tipo' => 'Tipo',
            'traduzir_para' => 'Traduzir para',
            'apostilamento' => 'Apostilamento',
            'reconhecimento_firma' => 'Reconhecimento de firma',
            'qtde_xerox_autenticado' => 'Quantidade de xerox autenticados',
            'qtde_xerox_simples' => 'Quantidade de xerox',
            'qtde_xerox_simples' => 'Xerox simples',
            'qtde_xerox_autenticado' => 'Xerox autenticado',
            'previa_digitalizada' => 'Prévia digitalizada',
            'reconhecimento_firma' => 'Reconhecimento de firma',
            'apostilamento' => 'Apostilamento',
            'pasta_protecao' => 'Pasta de proteção',
            'traducao_juramentada' => 'Tradução juramentada',
            'idioma_traducao_juramentada' => 'Idioma da tradução juramentada',
            'aviso_recebimento' => 'Avisto de recebimento (AR)',
            'tipo_inteiro_teor' =>  'Tipo de certidão de inteiro teor'
        ];
    }
}