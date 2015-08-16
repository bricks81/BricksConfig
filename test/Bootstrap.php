<?php

namespace BricksConfigTest;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_STRICT);

class Bootstrap {
	
	/**
	 *
	 * @var ServiceManager
	 */
	protected static $serviceManager;
	
	/**
	 *
	 * @var Bootstrap
	 */
	protected static $bootstrap;
	
	public static function init() {
		putenv('APP_ENV=phpunit');
		require './vendor/autoload.php';
		$serviceManager = new ServiceManager(new ServiceManagerConfig());
		$serviceManager->setService('ApplicationConfig', require './config/application.config.php');
		$serviceManager->get('ModuleManager')->loadModules();
		static::$serviceManager = $serviceManager;
	}
	
	/**
	 *
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public static function getServiceManager() {
		return static::$serviceManager;
	}
	
}

Bootstrap::init();