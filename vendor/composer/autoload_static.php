<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0c05f1d228e7d4a8a71cef90b5931644
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'ArashAbedii\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ArashAbedii\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0c05f1d228e7d4a8a71cef90b5931644::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0c05f1d228e7d4a8a71cef90b5931644::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0c05f1d228e7d4a8a71cef90b5931644::$classMap;

        }, null, ClassLoader::class);
    }
}
