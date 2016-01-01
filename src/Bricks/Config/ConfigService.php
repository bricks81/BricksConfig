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
use Bricks\Config\Config\Config as BricksConfig;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Bricks\Config\Config\ConfigInterface;

class ConfigService implements ConfigServiceInterface, EventManagerAwareInterface {
	
	/**
	 * @var string
	 */
	protected $defaultNamespace;
	
	/**
	 * @var \Zend\Config\Config
	 */
	protected $zconfig;
		
	/**
	 * @var EventManagerInterface
	 */
	protected $eventManager;
	
	/**
	 * @var array
	 */
	protected $loadedModules = array('BricksConfig');
	
	/**
	 * @var array
	 */
	protected $configs = array();
	
	/**
	 * @param string $namespace
	 */
	public function setDefaultNamespace($namespace){
		$this->defaultNamespace = $namespace;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultNamespace(){
		return $this->defaultNamespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
	 */
	public function setEventManager(EventManagerInterface $eventManager){
		$identifiers = $eventManager->getIdentifiers();
		if(false === array_search('BricksConfig',$identifiers)){
			$identifiers[] = 'BricksConfig';
		}
		$eventManager->setIdentifiers($identifiers);
		$this->eventManager = $eventManager;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
	 */
	public function getEventManager(){
		return $this->eventManager;
	}
	
	/**
	 * @param array $config
	 */
	public function setZendConfig(array $config){
		$this->zconfig = new ZendConfig($config,true);
	}
		
	/**
	 *  @return \Zend\Config\Config
	 */
	public function getZendConfig(){
		return $this->zconfig;
	}
	
	/**
	 * @param array $modules
	 */
	public function setLoadedModules(array $modules=array()){
		$this->loadedModules = $modules;
	}
	
	/**
	 * @return array
	 */
	public function getLoadedModules(){
		return $this->loadedModules;
	}
	
	/**
	 * @return array
	 */
	public function getNamespaces(){
		return array_keys($this->getZendConfig()->BricksConfig);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::set()
	 */
	public function setConfig(ConfigInterface $config){
		$module = $config->getModule();		
		$this->configs[$module] = $config;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigInterface::get()
	 */
	public function getConfig($moduleName=null){
		$defaultNamespace = $this->getDefaultNamespace();
		$namespace = $moduleName?:$defaultNamespace;
		if(!isset($this->configs[$namespace])){
			$class = $this->getZendConfig()->BricksConfig->$defaultNamespace->BricksConfig->configClass;
			if(isset($this->getZendConfig()->BricksConfig->$namespace->BricksConfig->configClass)){
				$class = $this->getZendConfig()->BricksConfig->$namespace->BricksConfig->configClass;
			}
			$config = new $class($this,$namespace);		
			if($config instanceof ConfigServiceAwareInterface){				
				$config->setConfigService($this);
			}
			if($config instanceof ConfigInterface){
				$config->setModule($moduleName);
				$config->setNamespace($namespace);
			}			
			$this->setConfig($config);
		}
		return $this->configs[$moduleName];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::get()
	 */
	public function get($path=null,$namespace=null){
	
		$parts = explode('.',$path);
		$namespace = $namespace?:$this->getDefaultNamespace();
		$defaultNamespace = $this->getDefaultNamespace();
		$zendConfig = $this->getZendConfig();
	
		if(
			!isset($zendConfig->BricksConfig->$defaultNamespace)
			&& !isset($zendConfig->BricksConfig->$namespace)
		){
			return;
		}
	
		if(isset($zendConfig->BricksConfig->$defaultNamespace)){
			$data = $zendConfig->BricksConfig->$defaultNamespace->toArray();
		}
		if(isset($zendConfig->BricksConfig->$namespace) && $namespace != $defaultNamespace){
			$data2 = $zendConfig->BricksConfig->$namespace->toArray();
		}
		if(isset($data) && isset($data2)){
			$data = array_replace_recursive($data,$data2);
		} else if(isset($data2)){
			$data = $data2;
		}
	
		if(null == $path){
			return $data;
		}
	
		// traverse path
		$value = null;
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
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::set()
	 */
	public function set($path,$value,$namespace=null){
	
		$namespace = $namespace?:$this->getDefaultNamespace();
		$parts = explode('.',$path);
	
		$zendConfig = $this->getZendConfig();
	
		if(!isset($zendConfig->BricksConfig->$namespace)){
			$zendConfig->BricksConfig->$namespace = new ZendConfig(array(),true);
		}
	
		$pointer = $zendConfig->BricksConfig->$namespace;
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
			$this->triggerBeforeSetEvent($path,$set);
			$pointer->$key = $set;
			$this->triggerAfterSetEvent($path);
		}
	
	}
	
	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $namespace	 
	 */
	protected function triggerBeforeSetEvent($path,$value,$namespace=null){
	
		if(null == $this->getEventManager()){
			return;
		}
	
		$parts = explode('.',$path);
		$realm = array_shift($parts);
		$namespace = $namespace?:$this->getDefaultNamespace();
	
		$var = $this->get($path);
		$parts = explode('.',$path);
		$_path = $realm;
		foreach($parts AS $key){
			$_path .= '.'.$key;
			$this->getEventManager()->trigger('BricksConfig::beforeSet('.$_path.')',$this,array(
				'calledPath' => $path,
				'currentPath' => $_path,
				'value' => $value,
				'namespace' => $namespace,
			));
	
		}
	
	}
	
	/**
	 * @param string $path
	 * @param string $namespace
	 */
	protected function triggerAfterSetEvent($path,$namespace=null){
	
		if(null == $this->getEventManager()){
			return;
		}
	
		$parts = explode('.',$path);
		$realm = array_shift($parts);
		$namespace = $namespace?:$this->getDefaultNamespace();
	
		$var = $this->get($path);
		$parts = explode('.',$path);
		$_path = $realm;
		foreach($parts AS $key){
			$_path .= '.'.$key;
			$this->getEventManager()->trigger('BricksConfig::afterSet('.$_path.')',$this,array(
				'calledPath' => $path,
				'currentPath' => $_path,
				'namespace' => $namespace,
			));
		}
	
	}
		
}