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
		),	
	);