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
	
	public function testArray(){
		
		$config = $this->getTestConfig();		
		
		$this->assertEquals('Bricks\Config\Config',$config->get('BricksConfig.configClass'));
		$this->assertEquals(true,$config->get('BricksConfig.testArray.multiple.bool'));
		$this->assertEquals(null,$config->get('BricksConfig.onlyHere'));
		
		$config->setNamespace('BricksConfigTest');		
		$this->assertEquals('Bricks\Config\Config2',$config->get('BricksConfig.configClass'));
		$this->assertEquals('test',$config->get('BricksConfig.onlyHere'));
		$config->resetNamespace();
		
		$config->setNamespace('BricksConfigTest2');
		$this->assertEquals('test2',$config->get('BricksConfig.onlyHere'));
		$config->resetNamespace();
		
	}
	
	public function testPath(){
		
		$config = $this->getTestConfig();				
		
		$this->assertTrue($config->get('BricksConfig.testArray.multiple.bool'));
		
		$array = $config->get('BricksConfig')['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array'));
		$array = &$array['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array.array'));
		$array = &$array['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array.array.array'));
		
	}
	
	public function testSet(){
		
		$config = $this->getTestConfig();
		$config->set('BricksConfig.array.array.array',false);
		$this->assertFalse($config->get('BricksConfig.array.array.array'));
		
		$config->setNamespace('BricksConfigTest');
		$config->set('BricksConfig.array.array.array',true);
		$this->assertTrue($config->get('BricksConfig.array.array.array'));
		$config->resetNamespace();
		
	}
	
}