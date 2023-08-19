<?php
namespace Komodo\Configurator;

/*
|-----------------------------------------------------------------------------
| Komodo Configurator
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: ConfigurationProvider.php
| Data da Criação Sat Aug 19 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Logger\Logger;

class ConfigurationProvider
{
    /** @var Logger */
    private static $logger;

    /** @var string */
    private static $path;

    /**
     * toObject
     *
     * @param array $array
     * @param string $class
     *
     * @return object|array
     */
    private static function toObject(array $array, $class = 'stdClass')
    {
        $object = new $class;
        $arr = [  ];

        if (!$array) {
            return [  ];
        }

        foreach ($array as $key => $value) {
            if (!is_string($key)) {
                $arr[  ] = $value;
                continue;
            }
            if (is_array($value)) {
                // Convert the array to an object
                $value = ConfigurationProvider::toObject($value, $class);
            }
            // Add the value to the object
            $object->{$key} = $value;
        }

        return $arr ?: $object;
    }

    private static function loadEnviroment($path = './', $file = '.env')
    {
        if (!file_exists('./.env')) {
            return self::$logger->trace('.env file not present');
        }
        $content = @\file_get_contents($path . $file);
        $vars = self::envParser($content);

        foreach ($vars as $var => $value) {
            $_ENV[ $var ] = $value;
        }
    }
    private static function envParser($content)
    {
        $vars = [  ];
        $content = preg_split('/\r\n|\r|\n/', $content);

        foreach ($content as $key => $value) {
            if (!$value || str_starts_with($value, '#')) {
                continue;
            }
            $v = explode('=', trim($value));

            $vars[ $v[ 0 ] ] = isset($v[ 1 ]) ? $v[ 1 ] : '';
        }

        return $vars;
    }

    public static function get($name)
    {
        $file = self::$path . "/$name.php";
        if (!is_file($file)) {
            return null;
        }

        $config = include $file;
        if (!is_array($config)) {
            $logger->trace([ $file, $config ], 'The configuration file is invalid. Files should by default return an associative array');
            throw new \Exception('The configuration file is invalid. Files should by default return an associative array');
        }
        return self::toObject($config);
    }

    /**
     * Method init
     *
     * @param string $pathConfigurationFiles $pathConfigurationFiles [explicite description]
     * @param Logger $logger $logger [explicite description]
     *
     * @return void
     */
    public static function init($pathConfigurationFiles, $envPath = './', $logger = null)
    {

        $logger = $logger ? clone $logger : new Logger();
        $logger->register(static::class);

        self::$path = $pathConfigurationFiles ?: __DIR__;
        self::$logger = $logger;
        $logger->trace([ $pathConfigurationFiles, $envPath ], 'Starting configuration feature');
        self::loadEnviroment($envPath);
    }
}
