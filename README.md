## About

Bricks Config is used to get an accessable and centralized configuration on top of the zend configuration.

Config contains an instance of your Zend Framework Application Configuration Object and adds the possibility to manipulate configuration parameters.

You can load or set the config values. The dot syntax allows you to define a "path" which will be mapped to the configurations array.
 
Each time you will use the set command. An Event will be triggered on which programs can listen to.

## Features
- Centralized configuration
- Namespaces

## Requires
Currently nothing required

### Installation

#### Using Composer

    php composer.phar require bricks81/bricks-config

#### Activate Modules

Add the Modules in your application.config.php:

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
	'__DEFAULT_NAMESPACE__' => array( // the default namespace
		'YourModule' => array(
			'configValue' => 'any string',
			'a' => array(
				'path' => array(
					'toA' => 'value',
				),
			),				
		),
	),
	'YourNamespace' => array( // as your namespace
		'YourModule' => array(
			'configValue' => 'overwritten for this namespace',			
		),
	),	
	// ...
```

### Example

This example will demonstrate the API that shouldn't change in future.

```php
	// ...
	// instantiate the config for your module
	$config = $serviceManager->get('BricksConfig')->getConfig('YourModuleNamespace');

	// get a value
	$config->get('a.path.toA');
	
	// set a value
	$config->set('a.path.toA','myValue');

```

## Note

Hope you will enjoy it.