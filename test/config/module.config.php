<?php

return array(
	'BricksConfig' => array(
		'__NO_NAMESPACE__' => array(
			'BricksConfig' => array(
				'configService' => 'Bricks\Config\ConfigService',
				'configClass' => 'Bricks\Config\Config\DefaultConfig',
			)
		),
		'__APPEND_NAMESPACE__' => array(
			'BricksConfigTest2' => array(
				'BricksConfigTest3',
			),
		),
		'__DEFAULT_NAMESPACE__' => array(
			'BricksConfig' => array(								
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
		'BricksConfigTest3' => array(
			'BricksConfig' => array(
				'testArray' => array(
					'multiple' => array(
						'onlyHere' => 'Test3',
					),
				),
			),
		),
	),
);