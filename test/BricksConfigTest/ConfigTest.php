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
		$this->assertInstanceOf('Bricks\Config\DefaultConfig',$config->getConfig('BricksConfig'));			
	}	
	
	public function testArray(){
		$config = $this->getTestConfig();		
		
		$cfg = $config->getConfig('BricksConfig');		
		$this->assertEquals('Bricks\Config\Config',$cfg->get('configClass'));
		$this->assertEquals('Bricks\Config\Config',$cfg->get('configClass','BricksConfig'));
		$this->assertEquals('Bricks\Config\Config2',$cfg->get('configClass','BricksConfigTest'));

		$this->assertEquals(true,$cfg->get('testArray.multiple.bool'));
		$this->assertEquals(true,$cfg->get('testArray.multiple.bool','BricksConfig'));
		$this->assertEquals(false,$cfg->get('testArray.multiple.bool','BricksConfigTest'));
		
		$this->assertEquals(null,$cfg->get('onlyHere'));
		$this->assertEquals('test',$cfg->get('onlyHere','BricksConfigTest'));
		$this->assertEquals('test2',$cfg->get('onlyHere','BricksConfigTest2'));
		
	}
	
	public function testPath(){
		$config = $this->getTestConfig();		
		$cfg = $config->getConfig('BricksConfig');
		$this->assertTrue($cfg->get('testArray.multiple.bool'));
		$this->assertFalse($cfg->get('testArray.multiple.bool','BricksConfigTest'));
		$array = Bootstrap::getServiceManager()->get('Config')['BricksConfig']['BricksConfig']['BricksConfig']['array'];
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