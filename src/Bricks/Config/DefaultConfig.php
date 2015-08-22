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