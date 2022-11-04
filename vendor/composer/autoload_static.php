<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1112ce2b1a04b9d66f5d40f1547b09c6
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1112ce2b1a04b9d66f5d40f1547b09c6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1112ce2b1a04b9d66f5d40f1547b09c6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1112ce2b1a04b9d66f5d40f1547b09c6::$classMap;

        }, null, ClassLoader::class);
    }
}
