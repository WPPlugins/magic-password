<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit21a74d7b929f64ef6355e21d4b2a5153
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TwoFAS\\UserZone\\' => 16,
            'TwoFAS\\MagicPassword\\' => 21,
            'TwoFAS\\Encryption\\' => 18,
            'TwoFAS\\' => 7,
        ),
        'E' => 
        array (
            'Endroid\\QrCode\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TwoFAS\\UserZone\\' => 
        array (
            0 => __DIR__ . '/..' . '/twofas/userzone-sdk/TwoFAS/UserZone',
        ),
        'TwoFAS\\MagicPassword\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'TwoFAS\\Encryption\\' => 
        array (
            0 => __DIR__ . '/..' . '/twofas/encryption/src',
            1 => __DIR__ . '/..' . '/twofas/encryption/tests',
        ),
        'TwoFAS\\' => 
        array (
            0 => __DIR__ . '/..' . '/twofas/sdk/TwoFAS',
        ),
        'Endroid\\QrCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/endroid/qrcode/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit21a74d7b929f64ef6355e21d4b2a5153::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit21a74d7b929f64ef6355e21d4b2a5153::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit21a74d7b929f64ef6355e21d4b2a5153::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
