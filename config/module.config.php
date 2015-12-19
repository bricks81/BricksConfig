<?php

	return array(
		'service_manager' => array(
			'factories' => array(
				'BricksConfig' => 'Bricks\Config\ServiceManager\ConfigFactory',
			),
		),		
		'BricksConfig' => array(
			'Test' => array(
				'BricksConfig' => array(
					'configClass' => 'Another',
				),				
			),
			'__DEFAULT_NAMESPACE__' => array(
				'BricksClassLoader' => array(
					'defaultFactories' => array(
						'BricksConfig_ConfigAwareInterface' => array(
							'Bricks\Config\ClassLoader\Factory\ConfigAwareFactory',
						),
					),
					'aliasMap' => array(
						'BricksConfig' => array(
							'configMapper' => 'Bricks\Config\Mapper\Config'
						),
					),					
				),
				'BricksConfig' => array(					
					'configClass' => 'Bricks\Config\Config',					
				),
				'BricksMapper' => array(
					'map' => array(
						'Bricks\Config\Model\Config' => array(
							'BricksConfig.configMapper',							
						),
					),
					'databases' => array(
						'__DEFAULT_SCHEMA__' => array(
							'table_prefix' => '',
							'tables' => array(
								'config' => array(
									'columns' => array(
										'config_id' => array(
											'type' => 'SERIAL',
										),
										'path' => array(
											'type' => 'VARCHAR',
											'length' => '128',
										),
										'namespace' => array(
											'type' => 'VARCHAR',
											'length' => '128',
										),
										'value_type' => array(
											'type' => 'ENUM',
											'values' => array(
												'default',
												'longtext',
											),
										),
										'value' => array(
											'type' => 'VARCHAR',
											'length' => 255,
										),
									),
									'constraints' => array(
										'primary_key' => array('config_id'),
										'unique' => array('path','namespace'),
									),
								),
								'config_type_longtext' => array(
									'columns' => array(
										'config_Id' => array(
											'type' => 'SERIAL',
										),
										'value' => array(
											'type' => 'LONGTEXT',
										),
									),
									'constraints' => array(
										'config.config_id' => array(
											'onUpdate' => 'CASCADE',
											'onDelete' => 'CASCADE',
										),
									),
								),
							),
						),
					),
				),
			),			
		),	
	);