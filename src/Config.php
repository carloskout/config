<?php
namespace Koutinh;

/**
 * FINALIDADE: Acessar e modificar as configurações definidas
 * no diretório src/config/*
 */
final class Config {

    private function __construct(){}

    /**
     * Recupera o valor de um determinado arquivo
     * de configuração.
     *
     * @param string $path - Caminho para acessar a configuração a
     * ser buscada. Padrão do path: As palavras devem ser separadas
     * por ponto. A primeira representa o nome do arquivo de configuração,
     * as demais representam as chaves de configuração que seram
     * recuperadas. Exemplo: database.mysql.user
     * 
     * @param mix $default - Valor padrão a ser retornado caso o valor
     * da configuração buscada não exista ou seja nulo.
     * 
     * @return mix - Retorna o valor encontrado ou o valor $default.
     */
    public static function get(string $path, $default = null) {
        $value = self::processPath($path, function($config, $keys) {
            return self::getValue($config, $keys);
        });

        if($value)
            return $value;
        return $default;
    }
    
    /**
     * Define o valor em um determinado arquivo
     * de configuração.
     *
     * @param string $path - Caminho da configuração a ser modificada.
     * Padrão do path: As palavras devem ser separadas
     * por ponto. A primeira representa o nome do arquivo de configuração,
     * as demais representam chaves de configurações a serem modificadas.
     * Exemplo: database.mysql.user
     * 
     * @param mix $newValue - O novo valor a ser definido para a configurão
     * especificada.
     * 
     * @return void
     */
    public static function set(string $path, $newValue):void {
        self::processPath($path, function(&$config, $keys, $args) {
            self::setValue($config, $keys, $args[0]);// $args[0] == $newValue
        }, $newValue);
    }

    /*
     * Processa o $path separando o nome do arquivo
     * de configuração das chaves a serem acessadas ou moficadas.
     * Após acessar o array especifico da configuração, executa
     * o callback passado por parâmetro.
     *
     * @param string $path - Exemplo: database.mysql.drive
     * @param mix $callback
     * @param array ...$args
     * @return mix|void
     */
    private static function processPath(string $path, $callback, ...$args) {
        if($path == '' || !preg_match('/[A-Za-z]+(\.[A-Za-z])+/', $path)) {
            return null;
        }

        $keys = explode('.', $path);
        $fileName = $keys[0];
        unset($keys[0]);

        $config = null;
    
        switch($fileName) {
            case 'app':
                $config = &$GLOBALS['app_config'];
            break;
            case 'database':
                $config = &$GLOBALS['db_config'];
            break;
        }

        if($callback && is_callable($callback)) {
            return $callback($config, $keys, $args);
        } else {
            return null;
        }


    }

    //Auxilia o metodo get a recuperar o valor da config. especificada
    private static function getValue($config, $keys) {
        if(empty($keys)) {
            return $config;
        }
        
        foreach($keys as $key => $value) {
            if(isset($config[$value])) {
                if(!is_array($config[$value])) {
                    return $config[$value];
                }
                unset($keys[$key]);
                return self::getValue($config[$value], $keys);
            } else {
                return;
            }
        }
    }

    //Auxilia o metodo set a modificar o valor da config. especificada
    private static function setValue(array &$config, array $keys, $newValue) {
        if(empty($keys)) {
            $config = $newValue;
        }

        foreach($keys as $key => $value) {
            if(isset($config[$value])) {
                if(!is_array($config[$value])) {
                    $config[$value] = $newValue;
                } else {
                    unset($keys[$key]);
                    self::setValue($config[$value], $keys, $newValue);
                }
            } else {
                return;
            }
        }
    }
}

