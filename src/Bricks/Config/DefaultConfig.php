<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksConfig
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Config;

class DefaultConfig implements ConfigInterface {
	
	/**
	 * @var Config
	 */
	protected $config;
	
	/**
	 * @var string
	 */
	protected $moduleName;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @param array $config
	 * @param string $moduleName
	 * @param string $defaultNamespace
	 */
	public function __construct(Config $config,$moduleName,$defaultNamespace=null){
		$this->setConfig($config);		
		$this->setModuleName($moduleName);
		$this->switchNamespace($defaultNamespace?:$moduleName);		
	}
	
	/**
	 * @param Config $config
	 */
	public function setConfig(Config $config){
		$this->config = $config;
	}
	
	/**
	 * @param string $module
	 * @param string $namespace
	 * @return DefaultConfig | Config
	 */
	public function getConfig($module=null,$namespace=null){
		return $this->config->getConfig($module,$namespace);
	}
	
	/**
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName){
		$this->moduleName = $moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	/**
	 * @param string $namespace
	 */
	public function switchNamespace($namespace=null){
		$this->namespace = $namespace;
	}
	
	/**
	 * @return string
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::getArray()
	 */
	public function getArray($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getConfig()->getArray($this->getModuleName(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::get()
	 */
	public function get($path,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getConfig()->get($path,$this->getModuleName(),$namespace);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::set()
	 */
	public function set($path,$value,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->getConfig()->set($path,$value,$this->getModuleName(),$namespace);
	}
	
}