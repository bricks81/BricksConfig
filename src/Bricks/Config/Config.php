<?php

/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 bricks-cms.org
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bricks\Config;

use Zend\Config\Config as ZendConfig;
use Zend\EventManager\EventManager;
use Zend\Mvc\Service\EventManagerFactory;
use Zend\EventManager\EventManagerInterface;

class Config {
	
	/**
	 * @var \Zend\Config\Config
	 */
	protected $zconfig;
	
	/**
	 * @var array
	 */
	protected $configs = array();
	
	/**
	 * @var \Zend\EventManager\EventManagerInterface
	 */
	protected $eventManager = null;
	
	/**
	 * @param $zconfig
	 */
	public function __construct($zconfig){
		$this->zconfig = new ZendConfig($zconfig,true);		
	}
	
	/**
	 * @param string $module
	 * @return DefaultConfig | Config
	 */
	public function getConfig($module=null,$namespace=null){
		if(null === $module){
			return $this;
		}
		$namespace = $namespace?:$module;		
		if(!isset($this->configs[$module][$namespace])){
			$class = $this->zconfig->BricksConfig->BricksConfig->BricksConfig->defaultConfigClass;
			if(isset($this->zconfig->BricksConfig->BricksConfig->$namespace->defaultConfigClass)){
				$class = $this->zconfig->BricksConfig->BricksConfig->$namespace->defaultConfigClass;
			}
			$this->configs[$module][$namespace] = new $class($this,$module);
		}
		return $this->configs[$module][$namespace];
	}
	
	/**
	 * @param \Zend\EventManager\EventManagerInterface $manager
	 */
	public function setEventManager(EventManagerInterface $manager){
		$this->eventManager = $manager;		
	}
	
	/**
	 * @return \Zend\EventManager\EventManagerInterface
	 */
	public function getEventManager(){
		return $this->eventManager;
	}
	
	/**
	 * @param array $data
	 * @param array $array
	 * @return array
	 */
	protected function mergeRecursive($data,$array){
		$return = $data;
		foreach($array AS $key => $value){
			if(is_array($value) && isset($data[$key])){
				$return[$key] = $this->mergeRecursive($data[$key], $array[$key]);
			} elseif(!isset($data[$key])) {
				$return[$key] = $value;
			}
		}
		return $return;
	}
	
	/**
	 * @param string $module
	 * @param string $namespace
	 * @return array
	 */
	public function getArray($module,$namespace=null){
		$data = array();
		$namespace = $namespace?:'BricksConfig';
		if(!isset($this->zconfig->BricksConfig->$module)){
			return $data;
		}
		if(!isset($this->zconfig->BricksConfig->$module->$module)){
			return $data;
		}
		$data = $this->zconfig->BricksConfig->$module->$module->toArray();
		if(isset($this->zconfig->BricksConfig->$module->$namespace)){
			$data = array_merge($data,$this->zconfig->BricksConfig->$module->$namespace->toArray());
		}
		foreach($this->zconfig->BricksConfig->$module->toArray() AS $ns => $d){
			$data = $this->mergeRecursive($data,$d);
		}		
		return $data;
	}
	
	/**
	 * @param string $path
	 * @param string $module
	 * @param string $namespace
	 * @return mixed | null
	 */
	public function get($path,$module,$namespace=null){
		
		// prepare data
		$data = $this->getArray($module,$namespace);
		
		// traverse path
		$value = null;
		$parts = explode('.',$path);
		$name = array_pop($parts);				
		$pointer = &$data;
		$current = array_shift($parts);		
		if(null === $current && isset($data[$name])){
			$value = $data[$name];
		} elseif( null !== $current){
			while(isset($pointer[$current])){				
				if(isset($pointer[$current][$name])){
					$value = $pointer[$current][$name];				
				}
				$pointer = &$pointer[$current];
				$current = array_shift($parts);
				if(null == $current){
					break;
				}
			}			
		}
		return $value;
		
	}
	
	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $module
	 * @param string $namespace
	 */
	public function set($path,$value,$module,$namespace=null){
		$namespace = $namespace?:$module;
		if(!isset($this->zconfig->BricksConfig->$module)){
			$this->zconfig->BricksConfig->$module = new ZendConfig(array(),true);
		}
		if(!isset($this->zconfig->BricksConfig->$module->$namespace)){
			$this->zconfig->BricksConfig->$module->$namespace = new ZendConfig(array(),true);
		}
		$pointer = $this->zconfig->BricksConfig->$module->$namespace;
		$parts = explode('.',$path);
		$key = array_pop($parts);
		$set = $value;
		if(0 == count($parts)){
			if(is_array($value)){
				$set = new ZendConfig($value,true);
			}
		} else {	
			foreach($parts AS $i){
				if(!isset($pointer->$i)){
					$pointer->$i = new ZendConfig(array(),true);
				}
				$pointer = &$pointer->$i;							
			}
			if(is_array($value)){
				$set = new ZendConfig($value,true);
			}
		}		
		if($pointer->$key instanceof ZendConfig){
			$before = clone $pointer->$key;
		} else {
			$before = $pointer->$key;
		}		
		if($pointer->$key != $set){
			$pointer->$key = $set;
			$this->triggerSetEvent($path,$module,$namespace);
		}
	}	
	
	/**
	 * Will be triggered if a variable has been setted
	 * Other classes can listen to this event in order to take
	 * action on there own if a config value changes
	 * 
	 * @param string $path
	 * @param mixed $before
	 * @param mixed $set
	 * @param string $module
	 * @param string $namespace
	 */
	protected function triggerSetEvent($path,$module,$namespace){
		if(null == $this->getEventManager()){
			return;
		}
		$var = $this->get($path,$module,$namespace);
		if($var instanceof Zend_Config){
			foreach($var AS $key => $value){
				if($value instanceof Zend_Config){
					$this->triggerSetEvent($path.'.'.$key,$module,$namespace);
				} else {
					$this->getEventManager()->trigger('BricksConfig::set('.$path.'.'.$key.')',$this,array(
						'module' => $module,
						'namespace' => $namespace
					));
				}
			}
		} else {
			$this->getEventManager()->trigger('BricksConfig::set('.$path.')',$this,array(
				'module' => $module,
				'namespace' => $namespace
			));
		}
	}
	
}