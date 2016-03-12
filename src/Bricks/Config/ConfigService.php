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
	 * @var string
	 */
	protected $noNamespace;
	
	/**
	 * @var string
	 */
	protected $appendNamespace;
	
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
	 * @see \Bricks\Config\ConfigServiceInterface::setNoNamespace()
	 */
	public function setNoNamespace($namespace){
		$this->noNamespace = $namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::getNoNamespace()
	 */
	public function getNoNamespace(){
		return $this->noNamespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::setAppendNamespace()
	 */
	public function setAppendNamespace($namespace){
		$this->appendNamespace = $namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::getAppendNamespace()
	 */
	public function getAppendNamespace(){
		return $this->appendNamespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::getAppendedNamespaces()
	 */
	public function getAppendedNamespaces($namespace){
		if(isset($this->getZendConfig()->BricksConfig->{$this->getAppendNamespace()}->$namespace)){
			$valid = $this->getNamespaces();
			$namespaces = $this->getZendConfig()->BricksConfig->{$this->getAppendNamespace()}->$namespace->toArray();
			foreach($namespaces AS $key => $namespace){
				if(false === array_search($namespace,$valid)){
					unset($namespaces[$key]);
				}
			}
			return $namespaces;
		}
		return array();
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
	 * @return array
	 */
	public function getNamespaces(){
		$namespaces = array_keys($this->getZendConfig()->BricksConfig->toArray());
		foreach($namespaces AS $key => $namespace){
			if(substr($namespace,0,2) == '__'){
				unset($namespaces[$key]);
			}
		}		
		return $namespaces;
	}	
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::set()
	 */
	public function setConfig(ConfigInterface $config){
		$this->configs[$config->getNamespace()] = $config;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigInterface::get()
	 */
	public function getConfig($namespace=null){
		$defaultNamespace = $this->getDefaultNamespace();
		$namespace = $namespace?:$defaultNamespace;
		if(!isset($this->configs[$namespace])){
			$class = $this->getZendConfig()->BricksConfig->{$this->getNoNamespace()}->BricksConfig->configClass;			
			$config = new $class($namespace);
			if($config instanceof ConfigServiceAwareInterface){				
				$config->setConfigService($this);
			}
			$this->setConfig($config);
		}
		return $this->configs[$namespace];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigServiceInterface::get()
	 */
	public function get($path=null,$namespace=null){
	
		$parts = explode('.',$path);
		$module = current($parts);
		$defaultNamespace = $this->getDefaultNamespace();
		$namespace = $namespace?:$defaultNamespace;		
		$zendConfig = $this->getZendConfig();
	
		$data = array();
		if($this->getNoNamespace() == $namespace || $this->getAppendNamespace() == $namespace){
			$data = $this->getZendConfig()->BricksConfig->$namespace->toArray();
		} else {			
			$namespaces = array($defaultNamespace,$namespace);
			$namespaces = array_merge($namespaces,$this->getAppendedNamespaces($namespace),array($this->getNoNamespace()));
			foreach($namespaces AS $_namespace){
				if(isset($zendConfig->BricksConfig->$_namespace)){
					$data = array_replace_recursive($data,$zendConfig->BricksConfig->$_namespace->toArray());
				}
			}
		}
		
		if(empty($data)){
			return;
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
	
		$pointer = &$zendConfig->BricksConfig->$namespace;
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
		if($pointer->$key !== $set){			
			$pathBefore = $path;
			$response = $this->triggerBeforeSetEvent($path,$set,$namespace);
			if($response && $response->stopped()){
				return;
			}			
			if($pathBefore != $path){				
				$this->set($path,$set,$namespace);
			} else {
				$pointer->$key = $set;
				$this->triggerAfterSetEvent($path,$before,$namespace);
			}			
		}
	
	}
	
	/**
	 * @param string &$path
	 * @param mixed &$value
	 * @param string &$namespace	 
	 */
	protected function triggerBeforeSetEvent(&$path,&$value,&$namespace=null){		
		if(null == $this->getEventManager()){
			return;
		}		
		$namespace = $namespace?:$this->getDefaultNamespace();
		$response = $this->getEventManager()->trigger('beforeSet',$this,array(
			'path' => &$path,
			'value' => &$value,
			'namespace' => &$namespace,
		));		
		return $response;
	}
	
	/**
	 * @param string $path
	 * @param string $valueBefore
	 * @param string $namespace
	 */
	protected function triggerAfterSetEvent($path,$valueBefore,$namespace=null){	
		if(null == $this->getEventManager()){
			return;
		}
		$namespace = $namespace?:$this->getDefaultNamespace();		
		$this->getEventManager()->trigger('afterSet',$this,array(
			'path' => $path,
			'valueBefore' => $valueBefore,
			'namespace' => $namespace,
		));
	}
		
}