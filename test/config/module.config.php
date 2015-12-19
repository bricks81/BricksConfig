<?php

return array(
	'BricksConfig' => array(
		'__DEFAULT_NAMESPACE__' => array(
			'BricksConfig' => array(
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
		),
		'BricksConfigTest' => array(
			'BricksConfig' => array(
				'configClass' => 'Bricks\Config\Config2',
				'testArray' => array(
					'multiple' => array(
						'bool' => false,
					),
				),		
				'onlyHere' => 'test',			
			),
		),
		'BricksConfigTest2' => array(
			'BricksConfig' => array(
				'onlyHere' => 'test2',
			),
		),		
	),
);