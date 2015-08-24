<?php

namespace BricksConfigTest;

use PHPUnit_Framework_TestCase;
use Zend\Config\Config as ZendConfig;
use Bricks\Config\Config;
class ConfigTest extends PHPUnit_Framework_TestCase {
	
	public function getTestConfig(ZendConfig $zconfig=null,$eventManager=null){
		$zconfig = $zconfig?:Bootstrap::getServiceManager()->get('Config');
		$config = new Config($zconfig);
		$config->setEventManager(
			$eventManager?:Bootstrap::getServiceManager()->get('EventManager')
		);
		return $config;
	}
	
	public function testGetInstance(){	
		$config = $this->getTestConfig();
		$this->assertInstanceOf('Bricks\Config\DefaultConfig',$cfg = $config->getConfig('BricksConfig'));			
	}	
	
	public function testArray(){
		$config = $this->getTestConfig();		
		$array = $config->getArray();
		$this->assertEquals($array,$this->testConfig);
		
		$cfg = $config->getConfig('BricksConfig');
		$array = $cfg->getArray();
		$this->assertEquals($array,$this->testConfig['BricksConfig']['BricksConfig']['BricksConfig']);

		$array = $cfg->getArray('BricksConfigTest');
		$this->assertEquals($array,array_replace_recursive(
			$this->testConfig['BricksConfig']['BricksConfig']['BricksConfig'],
			$this->testConfig['BricksConfig']['BricksConfig']['BricksConfigTest']
		));
	}
	
	public function testPath(){
		$config = $this->getTestConfig();		
		$cfg = $config->getConfig('BricksConfig');
		$this->assertTrue($cfg->get('testArray.multiple.bool'));
		$this->assertFalse($cfg->get('testArray.multiple.bool','BricksConfigTest'));
		$array = &$this->testConfig['BricksConfig']['BricksConfig']['BricksConfig']['array'];
		$this->assertEquals($array,$cfg->get('array'));
		$array = &$array['array'];
		$this->assertEquals($array,$cfg->get('array.array'));
		$array = &$array['array'];
		$this->assertEquals($array,$cfg->get('array.array.array'));
	}
	
	public function testSet(){
		$config = $this->getTestConfig();
		$cfg = $config->getConfig('BricksConfig');
		$cfg->set('array.array.array',false);
		$this->assertFalse($cfg->get('array.array.array'));
		$cfg->set('array.array.array',true,'BricksConfigTest');
		$this->assertTrue($cfg->get('array.array.array','BricksConfigTest'));
		
	}
	
}