<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksConfig
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Config;

use Zend\Config\Config as ZendConfig;
use Zend\EventManager\EventManager;

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
	 * @param ZendConfig $zconfig
	 */
	public function __construct(ZendConfig $zconfig){
		$this->zconfig = $zconfig;		
	}
	
	/**
	 * @param string $module
	 * @return Config
	 */
	public function getConfig($module=null,$namespace=null){
		if(null === $module){
			return $this;
		}
		$namespace = $namespace?:$module;
		if(!isset($this->configs[$module][$namespace])){
			$class = $this->zconfig->BricksConfig->BricksConfig->BricksConfig->defaultConfigClass;
			$this->configs[$module][$namespace] = new $class($this,$module);
		}
		return $this->configs[$module][$namespace];
	}
	
	/**
	 * @return \Zend\Config\Config
	 */
	public function getZendConfig(){
		return $this->zconfig;
	}
	
	/**
	 * @param string $module
	 * @return array
	 */
	public function getArray($module=null,$namespace=null){
		if(null === $module){
			return $this->zconfig->toArray();
		}
		$data = $this->zconfig->BricksConfig->$module->$module->toArray();
		if(null !== $namespace && isset($this->zconfig->BricksConfig->$module->$namespace)){
			$data = array_replace_recursive($data,$this->zconfig->BricksConfig->$module->$namespace->toArray());
		}
		return $data;
	}
	
	/**
	 * @param string $path
	 * @param string $module
	 * @param string $namespace
	 * @return mixed
	 */
	public function get($path,$module,$namespace=null){
		
		// prepare data
		$data = $this->getArray($module,$namespace);
		
		// traverse path
		$value = null;
		$parts = explode('.',$path);
		$name = array_pop($parts);
		$pointer = &$data;
		if(0 == count($parts)){
			if(isset($pointer[$name])){
				return $pointer[$name];
			}
			return null;
		}		
		foreach($parts AS $key){
			if(isset($pointer[$key][$name])){
				$value = $pointer[$key][$name];
			}
			if(isset($pointer[$key])){
				$pointer = &$pointer[$key];
			}
		}		
		if(isset($pointer[$name])){
			$value = $pointer[$name];
		}
		return $value;
	}
	
	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $module
	 * @param string $namespace
	 */
	/*
	public function set($path,$value,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		$pointer = $this->zconfig->BricksConfig->$module->$namespace;
		$parts = explode('.',$path);
		$key = array_pop($parts);
		if(0 == count($parts)){
			if(is_array($value)){
				$value = new ZendConfig($value,true);
			}
			$pointer->$key = $value;
			return;
		}		
		foreach($parts AS $i){
			if(!isset($pointer->$i)){
				$pointer->$i = new ZendConfig(array(),true);
			}
			$pointer = &$pointer->$i;			
		}
		if(is_array($value)){
			$value = new ZendConfig($value,true);
		}
		$pointer->$key = $value;
		$this->getEventManager()->trigger('BricksConfig::set',$this,array(
			'path' => $path,
			'value' => $value,
			'module' => $module,
			'namespace' => $namespace
		));
	}
	*/
	
}