<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9e61da9d0b458d25acd9942f3e72ec94
{
    public static $files = array (
        '841780ea2e1d6545ea3a253239d59c05' => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Q' => 
        array (
            'Qiniu\\' => 6,
        ),
        'P' => 
        array (
            'Predis\\' => 7,
        ),
        'L' => 
        array (
            'Library\\' => 8,
        ),
        'C' => 
        array (
            'Cli\\' => 4,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Qiniu\\' => 
        array (
            0 => __DIR__ . '/..' . '/qiniu/php-sdk/src/Qiniu',
        ),
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'Library\\' => 
        array (
            0 => __DIR__ . '/../..' . '/library',
        ),
        'Cli\\' => 
        array (
            0 => __DIR__ . '/../..' . '/cli',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9e61da9d0b458d25acd9942f3e72ec94::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9e61da9d0b458d25acd9942f3e72ec94::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
