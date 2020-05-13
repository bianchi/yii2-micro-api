<?php 

namespace api\models\forms\services\researches;

use yii\base\Model;

class Pesquisa extends Model {
    const FORMATO_FISICA = 'fisica';
    const FORMATO_ELETRONICA = 'eletronica';

    const TIPO_INTEIRO_TEOR = 'inteiroteor';
    const TIPO_SIMPLIFICADA = 'simplificada';

    const ENTIDADE_FISICA = 'fisica';
    const ENTIDADE_JURIDICA = 'juridica';
}