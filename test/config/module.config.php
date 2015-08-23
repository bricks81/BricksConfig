<?php

return array(
	'BricksConfig' => array( // Module to use
		'BricksConfig' => array( // Module to configure
			'BricksConfig' => array( // Namespace
				'configClass' => 'Bricks\Config\Config',
				'defaultConfigClass' => 'Bricks\Config\DefaultConfig',
				'testString' => 'test',
				'testArray' => array(
					'multiple' => array(
						'bool' => true,
					),
				),
				'array' => array(
					'array' => array(
						'array' => 123
					),
				),
			),
			'BricksConfigTest' => array(
				'testArray' => array(
					'multiple' => array(
						'bool' => false,
					),
				),					
			),
		),		
	),
);