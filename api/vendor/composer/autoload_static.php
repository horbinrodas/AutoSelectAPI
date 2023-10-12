<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita204f9401bb8a2d1afe52216d85b747e
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita204f9401bb8a2d1afe52216d85b747e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita204f9401bb8a2d1afe52216d85b747e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita204f9401bb8a2d1afe52216d85b747e::$classMap;

        }, null, ClassLoader::class);
    }
}