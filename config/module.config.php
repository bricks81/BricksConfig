<?php

	return array(
		'service_manager' => array(
			'factories' => array(
				'BricksConfig' => 'Bricks\Config\ServiceManager\ConfigFactory',
			),
		),		
		'BricksConfig' => array(
			/*
				'ModuleToConfigure' => array(
					'NamespaceUsed' => array(
						'configName' => 'configValue',
						'another' => array(
							'config' => 'value', // path: another.config
						),
					),
				),  
			*/
			'BricksConfig' => array(
				'BricksConfig' => array(
					/**
					 * For using classes please see BricksClassLoader
					 * This classes here are for startup conditions
					 */
					
					/**
					 * The service class to use
					 */
					'configClass' => 'Bricks\Config\Config',
									
					/**
					 * The class the module will get
					 */	
					'defaultConfigClass' => 'Bricks\Config\DefaultConfig',					
				),
				/*
				 'MyModule' => array( // your namespace
				 	'configClass' => 'My\Config\Class' class you will use in your module 
				 ), 
				*/
			),
		),
	);
?>