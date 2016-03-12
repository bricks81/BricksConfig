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

namespace Bricks\Config\Config;

use Bricks\Config\ConfigServiceAwareInterface;
use Bricks\Config\ConfigServiceInterface;

class DefaultConfig implements ConfigServiceAwareInterface, ConfigInterface {
	
	/**
	 * @var ConfigServiceInterface
	 */
	protected $configService;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @param string $namespace
	 */
	public function __construct($namespace){
		$this->namespace = $namespace;
	}
	
	/**
	 * @param ConfigServiceInterface $config
	 */
	public function setConfigService(ConfigServiceInterface $config){
		$this->configService = $config;
	}
	
	/**
	 * @return \Bricks\Config\ConfigServiceInterface
	 */
	public function getConfigService(){
		return $this->configService;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultNamespace(){
		return $this->getConfigService()->getDefaultNamespace();
	}	
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::getNoNamespace()
	 */
	public function getNoNamespace(){
		return $this->getConfigService()->getNoNamespace();
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::getAppendNamespace()
	 */
	public function getAppendNamespace(){
		return $this->getConfigService()->getAppendNamespace();
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::getAppendedNamespaces()
	 */
	public function getAppendedNamespaces(){
		$namespace = $this->getNamespace();
		return $this->getConfigService()->getAppendedNamespaces($namespace);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::getNamespace()
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::get()
	 */
	public function get($path=null){		
		return $this->getConfigService()->get($path,$this->getNamespace());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\Config\Config\ConfigInterface::set()
	 */
	public function set($path,$value){
		$this->getConfigService()->set($path,$value,$this->getNamespace());
	}
	
}