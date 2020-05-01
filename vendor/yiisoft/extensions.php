<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.10.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap/src',
    ),
  ),
  'yiisoft/yii2-gii' => 
  array (
    'name' => 'yiisoft/yii2-gii',
    'version' => '2.0.8.0',
    'alias' => 
    array (
      '@yii/gii' => $vendorDir . '/yiisoft/yii2-gii/src',
    ),
  ),
  'yiibr/yii2-br-validator' => 
  array (
    'name' => 'yiibr/yii2-br-validator',
    'version' => '1.1.0.0',
    'alias' => 
    array (
      '@yiibr/brvalidator' => $vendorDir . '/yiibr/yii2-br-validator/src',
      '@yiibr/brvalidator/tests' => $vendorDir . '/yiibr/yii2-br-validator/tests',
    ),
  ),
);
