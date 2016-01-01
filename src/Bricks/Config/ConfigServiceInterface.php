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

use Bricks\Config\Config\ConfigInterface;

interface ConfigServiceInterface {
	
	/**
	 * @return \Zend\Config\Config
	 */
	public function getZendConfig();
	
	/**
	 * @param array $zconfig
	 */
	public function setZendConfig(array $zconfig);
	
	/**
	 * @param string $namespace
	 */
	public function setDefaultNamespace($namespace);
	
	/**
	 * @return string
	 */
	public function getDefaultNamespace();
	
	/**
	 * @param array $modules
	 */
	public function setLoadedModules(array $modules=array());
	
	/**
	 * @return array
	 */
	public function getLoadedModules();
	
	/**
	 * @return array
	 */
	public function getNamespaces();
	
	/**
	 * @param ConfigInterface $config
	 */
	public function setConfig(ConfigInterface $config);
	
	/**
	 * @param string $moduleName as namespace
	 * @return ConfigInterface
	 */
	public function getConfig($moduleName=null);
	
	/**
	 * @param string $path
	 * @param string $namespace
	 */
	public function get($path=null,$namespace=null);
	
	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $namespace
	 */
	public function set($path,$value,$namespace=null);
	
}