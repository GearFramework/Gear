<?php

return [
    'bootstrap' => [],
    'modules' => [
        'app' => [
            'class' => [
                'name' => '\Demo\Demo',
                'components' => [
                    'router' => [
                        'defaultController' => 'home',
                        'routes' => [
                            'home' => '\Demo\Controllers\Home',
                        ],
                    ],
                ],
            ],
        ],
        'resources' => [
            'class' => [
                'name' => '\Gear\Modules\Resources\GResourcesModule',
                'plugins' => [
                    'js' => [
                        'mappingFolder' => '/js',
                        'hashingName' => true,
                        'safePath' => true,
                        'forceNoCache' => true,
                    ],
                    'css' => [
                        'mappingFolder' => '/css',
                        'hashingName' => false,
                        'forceNoCache' => true,
                    ],
                ],
            ],
        ],
    ],
    'components' => [],
    'properties' => [],
];
