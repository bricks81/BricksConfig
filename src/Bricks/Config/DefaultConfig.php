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
	 * @param array $config
	 * @param string $moduleName
	 */
	public function __construct(Config $config,$moduleName){
		$this->setConfig($config);		
		$this->setModuleName($moduleName);		
	}
	
	/**
	 * @param Config $config
	 */
	public function setConfig(Config $config){
		$this->config = $config;
	}
	
	/**
	 * @return \Bricks\Config\Config
	 */
	public function getConfig(){
		return $this->config;
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
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::getArray()
	 */
	public function getArray($namespace=null){
		return $this->getConfig()->getArray($this->getModuleName(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::get()
	 */
	public function get($path,$namespace=null){
		return $this->getConfig()->get($path,$this->getModuleName(),$namespace);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Config\ConfigInterface::set()
	 */
	public function set($path,$value,$namespace=null){
		$this->getConfig()->set($path,$value,$this->getModuleName(),$namespace);
	}
	
}