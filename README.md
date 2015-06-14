## About

Bricks Config contains an instance of your zend framework application configuration. All actions based on this config class will be made centralized.
You can load or set the config values. The dot syntax allows you to define a "path" which will be mapped to the configs array.
 

## Features
- Centralized configuration
- Namespaces

## Requires
current nothing required

### Installation

#### Using Composer

    php composer.phar require bricks81/bricks-config

#### Activate Modules

Add the modules in your application.config.php:

```php
	// ...    
	'modules' => array(
    	// ...
    	'BricksConfig',
    	'Application',
    	// ...	
    ),
	// ...
```

## Configuration

### What you've to do

Add the configuration for your module:

```php
	// ...
	'BricksConfig' => array(
		'YourModule' => array(
			'YourModule' => array( // as your namespace
				'configValue' => 'any string',
				'a' => array(
					'path' => array(
						'toA' => 'value',
					),
				),				
			),			
			'ForeignNamespace' => array(
				'configValue' => 'overwritten for this namespace',
			),
		),
	),	
	// ...
```

### Example

This example will demonstrate the api that shouldn't change in future.

```php
	// ...
	// instantiate the config for your module
	$config = $serviceManager->get('BricksConfig')->getConfig('YourModule');

	// get a value
	$config->get('a.path.toA',$optionalNamespace);
	
	// set a value
	$config->set('a.path.toA','myValue',$optionalNamespace);

	// get a array copy
	$config->getArray($optionalNamespace);

	// ...
```

## Note

Hope you will enjoy it.