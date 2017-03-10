<?php

use Bricks\Config;

return [
    'bricks' => [
        'default' => [
			'bricks-config' => [
			    'configClass' => 'Bricks\Config\Config',
				'observers' => [
					Config\Config::class.'::get.after' => [
						Config\Filter\ConfigParser::class
					]
				]
			]
		]
	]
];