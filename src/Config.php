<?php
namespace Koutinh\Util;

/**
 * Utilitário responsável por modificar e acessar parâmetros
 * de configurações definidos em arquivos config na raiz do
 *  projeto. config/databse.php
 */
final class Config {

    /**
     * Array associativo onde as chaves correspondem aos nomes
     * dos arquivos de configurações e os valores correspondem
     * aos nomes de variáveis que armazenam os parâmetros de
     * configurações dentro dos respectivos arquivos de config.
     *
     * @var array
     */
    private static $filesConfig = array(
        'database' => 'db_config',
        'app' => 'app_config'
    );

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
     * @param string $path - Caminho da configuração a ser modificado.
     * Padrão do path: As palavras devem ser separadas
     * por ponto. A primeira representa o nome do arquivo de configuração,
     * as demais representam chaves de configurações a serem modificadas.
     * Exemplo: database.mysql.user
     * 
     * @param mix $newValue - O novo valor a ser definido para a configurão
     * especificada.
     * 
     * @return boolean
     */
    public static function set(string $path, $newValue) {
        return self::processPath($path, function(&$config, $keys, $args) {
            return self::setValue($config, $keys, $args[0]);// $args[0] == $newValue
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
        if($path == '' || !preg_match('/[A-Za-z]+[0-9]*(\.[A-Za-z]+[0-9]*)+/', $path)) {
            return null;
        }

        $keys = explode('.', $path);
        $fileName = $keys[0];
        unset($keys[0]);
        $config = null;

        if(isset(self::$filesConfig[$fileName])) {
            $config = &$GLOBALS[self::$filesConfig[$fileName]];
            if(!$config)
                return null;
        } else {
            return null;
        }

        if($callback && is_callable($callback)) {
            return $callback($config, $keys, $args);
        } else {
            return null;
        }

    }

    /**
     * Método recursivo que busca valores de parâmetros
     * especificados.
     *
     * @param array $config - Array de configuração
     * @param array $keys - Conjunto de palavras divididas por ponto. 
     * Cada palavra corresponde a uma chave a ser percorrida no array config.
     * 
     * @return mix - Valor do parâmetro encontrado.
     */
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
                return null;
            }
        }
    }

    /**
     * Método recursivo que faz alteração no parâmetro de configuração
     * especificado.
     *
     * @param array $config - Array de configuração
     * 
     * @param array $keys - Conjunto de palavras divididas por ponto. 
     * Cada palavra corresponde a uma chave a ser percorrida no array config.
     * 
     * @param mix $newValue - Novo valor que irá substituir o valor atual do 
     * parâmentro de configuração encontrado.
     * 
     * @return boolean - Retorna true caso haja a alteração do parâmetro.
     * Retorna false caso contrário.
     */
    private static function setValue(array &$config, array $keys, $newValue):bool {
        if(empty($keys)) {
            $config = $newValue;
            return true;
        }

        foreach($keys as $key => $value) {
            if(isset($config[$value])) {
                if(!is_array($config[$value])) {
                    $config[$value] = $newValue;
                    return true;
                } else {
                    unset($keys[$key]);
                    return self::setValue($config[$value], $keys, $newValue);
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Sobreescreve os valores padrões dos arquivos de configurações.
     *
     * @param array $opts - Array associativo onde as chaves correspondem
     * aos nomes do arquivos de configurações e os valores correspondem
     * as nomes de variáveis dentro desses arquivos de configurações.
     * @return void
     */
    public static function addFilesConfig(array $opts):void {
        self::$filesConfig = !empty($opts)? $opts : self::$filesConfig;
    }
}

