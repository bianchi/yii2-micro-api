<?php 

namespace api\models\forms\services\certificates;

use yii\base\Model;

class Certidao extends Model {
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
}