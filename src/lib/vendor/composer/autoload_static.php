<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteaf6f116b08ae520de7e692da2ad3ea6
{
    public static $files = array (
        'f084d01b0a599f67676cffef638aa95b' => __DIR__ . '/..' . '/smarty/smarty/libs/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteaf6f116b08ae520de7e692da2ad3ea6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteaf6f116b08ae520de7e692da2ad3ea6::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
