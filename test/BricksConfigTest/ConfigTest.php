<?php

namespace Bricks\Config;

use Bricks\Config\Config\DefaultConfig;
class Config2 extends DefaultConfig {}

namespace BricksConfigTest;

use PHPUnit_Framework_TestCase;
use Zend\Config\Config as ZendConfig;
use Bricks\Config\ConfigService;
use Bricks\Config\ConfigServiceAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;

class ConfigTest extends PHPUnit_Framework_TestCase {
	
	public function getConfigService(ZendConfig $zconfig=null,$eventManager=null){
		$zconfig = $zconfig?:Bootstrap::getServiceManager()->get('Config');
		$em = $eventManager?:Bootstrap::getServiceManager()->get('EventManager');		
		$service = new ConfigService();
		$service->setZendConfig($zconfig);
		$service->setDefaultNamespace('__DEFAULT_NAMESPACE__');				
		$service->setEventManager($em);		
		return $service;
	}
	
	public function testNamespaces(){
		
		$service = $this->getConfigService();		
		
		$config = $service->getConfig('BricksConfig');
		
		$this->assertEquals('BricksConfig',$config->getNamespace());		
		$this->assertEquals('Bricks\Config\Config\DefaultConfig',$config->get('BricksConfig.configClass'));
		$this->assertEquals(true,$config->get('BricksConfig.testArray.multiple.bool'));
		$this->assertEquals(null,$config->get('BricksConfig.onlyHere'));
		
		$config = $service->getConfig('BricksConfigTest');
		$this->assertEquals('Bricks\Config\Config2',$config->get('BricksConfig.configClass'));
		$this->assertEquals('test',$config->get('BricksConfig.onlyHere'));		
		
		$config = $service->getConfig('BricksConfigTest2');
		$this->assertEquals('test2',$config->get('BricksConfig.onlyHere'));		
		
		$config = $service->getConfig('BricksConfigTest');
		$this->assertEquals('BricksConfigTest',$config->getNamespace());
		$this->assertEquals('Bricks\Config\Config2',$config->get('BricksConfig.configClass'));
		$this->assertEquals('test',$config->get('BricksConfig.onlyHere'));
		
		$config = $service->getConfig('BricksConfigTest2');
		$this->assertEquals('BricksConfigTest2',$config->getNamespace());
		$this->assertEquals('test2',$config->get('BricksConfig.onlyHere'));
		
		$config = $service->getConfig('BricksConfig');
		$this->assertEquals('Bricks\Config\Config\DefaultConfig',$config->get('BricksConfig.configClass'));
		$this->assertEquals(true,$config->get('BricksConfig.testArray.multiple.bool'));
		$this->assertEquals(null,$config->get('BricksConfig.onlyHere'));

		$config = $service->getConfig('BricksConfigTest');
		$this->assertEquals('BricksConfigTest',$config->getNamespace());
		$this->assertEquals('Bricks\Config\Config2',$config->get('BricksConfig.configClass'));
		$this->assertEquals('test',$config->get('BricksConfig.onlyHere'));
		
		$config = $service->getConfig('BricksConfigTest2');
		$this->assertEquals('BricksConfigTest2',$config->getNamespace());
		$this->assertEquals('test2',$config->get('BricksConfig.onlyHere'));
		
	}
	
	public function testPath(){
		
		$service = $this->getConfigService();
		$config = $service->getConfig('BricksConfig');
		$this->assertEquals('BricksConfig',$config->getNamespace());
		
		$this->assertTrue($config->get('BricksConfig.testArray.multiple.bool'));		
		$array = $config->get('BricksConfig')['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array'));
		$array = &$array['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array.array'));
		$array = &$array['array'];
		$this->assertEquals($array,$config->get('BricksConfig.array.array.array'));
		
	}
	
	public function testSet(){
		
		$service = $this->getConfigService();
		$config = $service->getConfig('__DEFAULT_NAMESPACE__');
		$this->assertEquals('__DEFAULT_NAMESPACE__',$config->getNamespace());
		
		$config->set('BricksConfig.array.array.array',false);
		$this->assertFalse($config->get('BricksConfig.array.array.array'));
		
		$config = $service->getConfig('BricksConfigTest');
		$config->set('BricksConfig.array.array.array',true);
		$this->assertTrue($config->get('BricksConfig.array.array.array'));
				
	}
	
	/**
	 * @depends testSet
	 */
	public function testListeners(){		
		$service = $this->getConfigService();
		$config = $service->getConfig('AnotherOne');
		$em = $service->getEventManager();		
		$em->getSharedManager()->attach('BricksConfig','beforeSet',function($e){
			if('BricksConfig.array.array'!=$e->getParam('path')){
				$this->assertEquals('BricksConfig.array.array.array',$e->getParam('path'));
			}			
			$params = $e->getParams();
			$params['path'] = 'BricksConfig.array.array';
			$params['value'] = 'Another';			
		});
		$em->getSharedManager()->attach('BricksConfig','afterSet',function($e){
			$this->assertEquals('BricksConfig.array.array',$e->getParam('path'));
			$this->assertEquals(null,$e->getParam('value'));			
		});
		$config->set('BricksConfig.array.array.array',false);		
		
		
	}
	
}