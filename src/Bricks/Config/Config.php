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
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

class Config implements ConfigInterface, EventManagerAwareInterface {
	
	/**
	 * @var string
	 */
	protected $defaultNamespace = '__DEFAULT_NAMESPACE__';
	
	/**
	 * @var string
	 */
	protected $namespace = '__DEFAULT_NAMESPACE__';
	
	/**
	 * @var array
	 */
	protected $namespaceResetStack = array();
	
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
	public function __construct($zconfig,$defaultNamespace=null){
		$this->zconfig = new ZendConfig($zconfig,true);		
		if($defaultNamespace){
			$this->setDefaultNamespace($namespace);
		}
	}
	
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
	 * @see \Bricks\Config\ConfigInterface::setNamespace()
	 */
	public function setNamespace($namespace){
		array_push($this->namespaceResetStack,$this->getNamespace());
		$this->namespace = $namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigInterface::getNamespace()
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigInterface::resetNamespace()
	 */
	public function resetNamespace(){
		if(count($this->namespaceResetStack)){
			$namespace = array_pop($this->namespaceResetStack);
		} else {
			$namespace = $this->getDefaultNamespace();
		}
		$this->setNamespace($namespace);
	}
	
	/**
	 *  @return \Zend\Config\Config
	 */
	public function getZendConfig(){
		return $this->zconfig;
	}
	
	/**
	 * @param EventManagerInterface $manager
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
	 * {@inheritDoc}
	 * @see \Bricks\Config\ConfigInterface::get()
	 */
	public function get($path,$namespace=null){
		
		if(null!==$namespace){
			$this->setNamespace($namespace);
		}
		
		$parts = explode('.',$path);
		$module = array_shift($parts);		
		$_namespace = $this->getNamespace();
		$defaultNamespace = $this->getDefaultNamespace();
		
		if(!isset($this->getZendConfig()->BricksConfig->$defaultNamespace->$module)){
			return;
		}
		
		$data = $this->getZendConfig()->BricksConfig->$defaultNamespace->$module->toArray();
		
		if(isset($this->getZendConfig()->BricksConfig->$_namespace->$module) && $_namespace != $defaultNamespace){
			$data = array_replace_recursive($data,$this->getZendConfig()->BricksConfig->$_namespace->$module->toArray());
		}
		
		if($path == $module){
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
		
		if(null != $namespace){
			$this->resetNamespace();
		}
		
		return $value;
		
	}
	
	/**
	 * @param string $path
	 * @param mixed $value	 	 
	 * @param string $namespace
	 */
	public function set($path,$value,$namespace=null){
		
		if(null != $namespace){
			$this->setNamespace($namespace);
		}
		
		$parts = explode('.',$path);
		$module = array_shift($parts);
		$_namespace = $this->getNamespace();
		
		if(!isset($this->getZendConfig()->BricksConfig->$_namespace->$module)){
			$this->getZendConfig()->BricksConfig->$_namespace->$module = new ZendConfig(array(),true);
		}
		
		$pointer = $this->getZendConfig()->BricksConfig->$_namespace->$module;		
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
		
		if(null != $namespace){
			$this->resetNamespace();
		}
		
	}	
	
	/**
	 * Will be triggered if a variable has been setted
	 * Other classes can listen to this event in order to take
	 * action on there own if a config value changes
	 * 
	 * @param string $path
	 */
	protected function triggerBeforeSetEvent($path,$value){
		
		if(null == $this->getEventManager()){
			return;
		}
		
		$parts = explode('.',$path);
		$module = array_shift($parts);
		$namespace = $this->getNamespace();
		
		$var = $this->get($path);
		$parts = explode('.',$path);
		$_path = $module;
		foreach($parts AS $key){
			$_path .= '.'.$key;
			$this->getEventManager()->trigger('BricksConfig::beforeSet('.$_path.')',$this,array(
				'calledPath' => $path,
				'currentPath' => $_path,
				'value' => $value,
				'module' => $module,
				'namespace' => $namespace,
			));
			
		}
		
	}
	
	/**
	 * @param string $path
	 */
	protected function triggerAfterSetEvent($path){
		
		if(null == $this->getEventManager()){
			return;
		}
		
		$parts = explode('.',$path);
		$module = array_shift($parts);
		$namespace = $this->getNamespace();
		
		$var = $this->get($path);
		$parts = explode('.',$path);
		$_path = $module;
		foreach($parts AS $key){
			$_path .= '.'.$key;
			$this->getEventManager()->trigger('BricksConfig::afterSet('.$_path.')',$this,array(
				'calledPath' => $path,
				'currentPath' => $_path,				
				'module' => $module,
				'namespace' => $namespace,
			));				
		}
		
	}
	
}