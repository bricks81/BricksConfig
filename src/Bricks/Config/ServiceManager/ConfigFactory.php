<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksConfig
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Config\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;

class ConfigFactory implements FactoryInterface {

	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $sl){
		$cfg = $sl->get('Config');		
		$configClass = $cfg['BricksConfig']['BricksConfig']['BricksConfig']['configClass'];
		$service = new $configClass(new Config($cfg,true),$sl->getEventManager());
		return $service;
	}
	
}