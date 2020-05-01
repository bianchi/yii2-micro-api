<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2cff35b77ba768570c0f04d1f088a355
{
    public static $files = array (
        '2cffec82183ee1cea088009cef9a6fc3' => __DIR__ . '/..' . '/ezyang/htmlpurifier/library/HTMLPurifier.composer.php',
    );

    public static $prefixLengthsPsr4 = array (
        'y' => 
        array (
            'yiibr\\brvalidator\\tests\\' => 24,
            'yiibr\\brvalidator\\' => 18,
            'yii\\gii\\' => 8,
            'yii\\composer\\' => 13,
            'yii\\bootstrap\\' => 14,
            'yii\\' => 4,
        ),
        'c' => 
        array (
            'cebe\\markdown\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'yiibr\\brvalidator\\tests\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiibr/yii2-br-validator/tests',
        ),
        'yiibr\\brvalidator\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiibr/yii2-br-validator/src',
        ),
        'yii\\gii\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2-gii/src',
        ),
        'yii\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2-composer',
        ),
        'yii\\bootstrap\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2-bootstrap/src',
        ),
        'yii\\' => 
        array (
            0 => __DIR__ . '/..' . '/yiisoft/yii2',
        ),
        'cebe\\markdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/cebe/markdown',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'HTMLPurifier' => 
            array (
                0 => __DIR__ . '/..' . '/ezyang/htmlpurifier/library',
            ),
        ),
        'D' => 
        array (
            'Diff' => 
            array (
                0 => __DIR__ . '/..' . '/phpspec/php-diff/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2cff35b77ba768570c0f04d1f088a355::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2cff35b77ba768570c0f04d1f088a355::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit2cff35b77ba768570c0f04d1f088a355::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
