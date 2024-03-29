<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde75c3a3e59ec62f0e58da19358d3084
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'AloPeyk\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'AloPeyk\\' => 
        array (
            0 => __DIR__ . '/..' . '/alopeyk/alopeyk-api-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde75c3a3e59ec62f0e58da19358d3084::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde75c3a3e59ec62f0e58da19358d3084::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitde75c3a3e59ec62f0e58da19358d3084::$classMap;

        }, null, ClassLoader::class);
    }
}
