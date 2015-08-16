<?php

namespace BricksConfigTest;

use PHPUnit_Framework_TestCase;
use Zend\Config\Config as ZendConfig;
use Bricks\Config\Config;
class ConfigTest extends PHPUnit_Framework_TestCase {
	
	protected $testConfig = array(
		'BricksConfig' => array(
			'BricksConfig' => array(
				'BricksConfig' => array(
					'configClass' => 'Bricks\Config\Config',
					'defaultConfigClass' => 'Bricks\Config\DefaultConfig',
					'testString' => 'test',
					'testArray' => array(
						'multiple' => array(
							'bool' => true,
						),
					),
					'array' => array(
						'array' => array(
							'array' => 123
						),
					),
				),
				'BricksConfigTest' => array(
					'testArray' => array(
						'multiple' => array(
							'bool' => false,
						),
					),					
				),
			),
		),
	);
	
	public function getTestConfig(){
		$config = new Config(
				new ZendConfig($this->testConfig,true),
				Bootstrap::getServiceManager()->get('EventManager')
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