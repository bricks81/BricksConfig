<?php

	return array(
		'service_manager' => array(
			'factories' => array(
				'BricksConfig' => 'Bricks\Config\ServiceManager\ConfigFactory',
			),
		),		
		'BricksConfig' => array(			
			'BricksConfig' => array( // Module to configure
				'BricksConfig' => array( // Namespace
					'configClass' => 'Bricks\Config\Config',
					'defaultConfigClass' => 'Bricks\Config\DefaultConfig',					
				),
			),
			'BricksModelMapper' => array(
				'BricksConfig' => array(
					'Mapper' => array(
						'adapterMap' => array(
							'Bricks\Config\Mapper\Config' => array(
								'readAdapter' => 'Zend\Db\Adapter\Adapter',
								'writeAdapters' => 'Zend\Db\Adapter\Adapter',								
							),
						),
					),
				),
			),
		),	
	);