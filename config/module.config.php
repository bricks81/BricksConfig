<?php

use Bricks\Config;

return [
    'service_manager' => [
        'factories' => [
            Config\Config::class => Config\ConfigFactory::class
        ]
    ],
    'controllers' => [
        'initializers' => [
            Config\Config::class => Config\ConfigInitializer::class
        ]
    ],
];