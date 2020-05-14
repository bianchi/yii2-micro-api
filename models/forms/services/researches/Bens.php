<?php 

namespace api\models\forms\services\researches;

use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

class Bens extends Pesquisa {
    public $uf;
    public $cidade;
    public $nivel;
    public $ids_cartorios;

    public function rules()
    {
        return [
            [['uf'], 'string', 'max' => 2],
            [['cidade'], 'string', 'max' => 255],
            [['nivel'], 'in', 'range' => [self::NIVEL_SIMPLES, self::NIVEL_COMPLETA]],
        ];
    }
}