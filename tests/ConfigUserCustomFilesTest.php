<?php

use PHPUnit\Framework\TestCase;
use Koutinh\Util\Config;

class ConfigUserCustomFilesTest extends TestCase {

    protected function setUp():void {
        $GLOBALS['sockets_config'] = array(
            'host'=> 'localhost',
            'port' => '3009'
        ); 

       Config::addFilesConfig(array(
           'network' => 'sockets_config'
       ));
    }

    public function testRetornoDiferenteDeNull() {
        $this->assertNotNull(Config::get('network.host'));
    }
    
}