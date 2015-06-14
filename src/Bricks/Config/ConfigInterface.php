<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksConfig
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Config;

interface ConfigInterface {
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getArray($namespace=null);
	
	/**
	 * @param string $path
	 * @param string $namespace
	 */
	public function get($path,$namespace=null);
	
	/**
	 * @param string $path
	 * @param mixed $value
	 * @param string $namespace
	 */
	public function set($path,$value,$namespace=null);
	
}