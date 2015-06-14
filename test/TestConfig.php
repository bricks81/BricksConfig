<?php
return array(
    'modules' => array(    	
    	'BricksConfig',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local}.php',
        	'../../../config/autoload/phpunit.php',
        	'./config/module.config.php',        	
        ),
        'module_paths' => array(
            '../../../module',
            '../../../vendor',
        ),
    ),
);