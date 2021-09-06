<?php
namespace Koutinh\Tests;

use PHPUnit\Framework\TestCase;
use Koutinh\Util\Config;

class ConfigTest extends TestCase {

    protected function setUp():void {
        $GLOBALS['db_config'] = array(
            'drivers' => array(
                'mysql' => array(
                    'drive' => 'mysql',
                    'host' => 'localhost',
                    'db_name' => 'nfe_project',
                    'user' => 'koutinh',
                    'pass' => 'charset=UTF8',
                    'persistent_connection' => true
                ),
            ),
        
            'encoding' => 'UTF8'
        ); 
    }

    /**************************************************
     * Testando o acesso a parâmetros de configuração *
     **************************************************/
    
    public function testRetornoNullParaChaveVazia() {
        $this->assertNull(Config::get(''));
    }

    public function testRetornoNullParaChaveComPadraoInvalido() {
        $this->assertNull(Config::get('123.er3.124'));
    }

    public function testRetornoNullParaArquivoConfigNaoExistente() {
        $this->assertNull(Config::get('arquivonaoexiste.encoding'));
    }

    public function testRetornoStringParaParametroConfigValido() {
        $this->assertIsString(Config::get('database.encoding'));
    }

    public function testRetornoArrayParaParamentroConfigValido() {
        $this->assertIsArray(Config::get('database.drivers'));
    }

    public function testRetornoValorDefaultParaParametroConfigNaoEncontrado() {
        $this->assertEquals('innoDB', Config::get('database.engine', 'innoDB'));
    }

    /******************************************************
     * Testando a definição de parâmetros de configuração *
     ******************************************************/

    public function testAlteracaoParametro() {
        Config::set('database.drivers.mysql.host', '192.168.14.203');
        $this->assertEquals('192.168.14.203', Config::get('database.drivers.mysql.host'));
    }

    public function testAlteracaoParamentroArray() {
        Config::set('database.drivers', 'Substituicao de array por string');
        $this->assertIsString(Config::get('database.drivers'));
    }

    public function testRetornoFalseParaParamtroConfigNaoExistente() {
        $this->assertFalse(Config::set('database.drivers.oracle', 'oracle11i'));
    }
}