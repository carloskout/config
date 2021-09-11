<?php

use PHPUnit\Framework\TestCase;
use Koutinh\Util\Config;

class ConfigUserCustomFilesTest extends TestCase {

    protected function setUp():void {
        $GLOBALS['sockets_config'] = array(
            'host'=> 'localhost',
            'port' => '3009'
        ); 

        $GLOBALS['app_config'] = array(
            'base_site' => 'https://nacionalbuy.inc.com'
        );

       //key   = nome do arquivo
       //value = nome da variável array que armazana as configurações
       Config::addFilesConfig(array(
           'network' => 'sockets_config',
           'app123' => 'app_config'
       ));
    }

    public function testConfigValida() {
        $this->assertNotNull(Config::get('network.host'));
    }

    public function testConfigComNomeArquivoAlfanum() {
        $this->assertNotNull(Config::get('app123.base_site'));
    }
    
}