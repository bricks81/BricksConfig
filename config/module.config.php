<?php

	return array(
		'service_manager' => array(
			'factories' => array(
				'BricksConfig' => 'Bricks\Config\ServiceManager\ConfigServiceFactory',
			),
		),		
		'BricksConfig' => array(
			'defaultNamespace' => '__DEFAULT_NAMESPACE__',
			/*
			'Test' => array(
				'BricksConfig' => array(
					'configClass' => 'Another',
				),				
			),
			*/
			'__DEFAULT_NAMESPACE__' => array(
				'BricksClassLoader' => array(
					'defaultFactories' => array(
						'Bricks\Config\ClassLoader\Factories\ConfigAwareFactory',
					),										
				),
				'BricksConfig' => array(					
					'configService' => 'Bricks\Config\ConfigService',
					'configClass' => 'Bricks\Config\Config\DefaultConfig',
					'defaultDatabaseConfig' => array(
						'mysqli' => array(
							'engine' => 'InnoDB',
							'charset' => 'utf8',
							'collation' => 'utf8_general_ci',
						)
					)
				),
				'BricksMapper' => array(					
					'databases' => array(
						'__DEFAULT_SCHEMA__' => array(
							/*
							'databaseConfig' => array(
								'mysqli' => array(									
								),
							),							
							'tablePrefix' => '',
							*/
							'tables' => array(
								'config' => array(
									'columns' => array(
										'config_id' => array('dataType' => 'SERIAL'),
										'path' => array(
											'dataType' => 'VARCHAR',
											'characterMaximumLength' => '128',
										),
										'namespace' => array(
											'dataType' => 'VARCHAR',
											'characterMaximumLength' => '128',
										),
										'value_type' => array(
											'dataType' => 'VARCHAR',
											'characterMaximumLength' => '64',
											'defaultValue' => 'VARCHAR',
										),																				
									),
									/*
									'tableConfig' => array(										
									),
									*/
								),
								'config_value_default' => array(
									'columns' => array(
										'config_id' => array('dataType' => 'SERIAL'),
										'value' => array(
											'dataType' => 'VARCHAR',
											'characterMaximumLength' => '255',
										),
									),									
								),
								'config_value_longtext' => array(
									'columns' => array(
										'config_id' => array('dataType' => 'SERIAL'),
										'max_length' => array('dataType' => 'INT'),
										'value' => array('dataType' => 'LONGTEXT'),
									),
								),					
							),
							'constraints' => array(
								'config' => array(
									'primary_key' => array(
										'isPrimaryKey' => true,
										'columns' => array('config_id'),
									),
									'unique_path' => array(
										'isUnique' => true,
										'columns' => array('path','namespace'),
									)
								),								
							),
						),
					),
				),
			),			
		),	
	);