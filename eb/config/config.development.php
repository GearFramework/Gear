<?php

return [
    'modules' => [
        'app' => [
            'class' => [
                'name' => '\eb\ElectroBunker',
                'components' => [
                    'controllers' => [
                        'defaultControllerName' => 'home',
                        'mapControllers' => [
                            'operators/auth' => '\eb\controllers\operators\AuthController',
                            'operators/login' => '\eb\controllers\operators\AuthController',
                        ],
                    ],
                ],
            ],
        ],
        'resources' => [
            'class' => [
                'name' => '\gear\modules\resources\GResourcesModule',
                'plugins' => [
                    'js' => ['mappingFolder' => 'js'],
                    'css' => ['mappingFolder' => 'css'],
                ],
            ],
        ],
        'operators' => [
            'class' => [
                'name' => '\gear\modules\user\GUserModule',
                'components' => [
                    'userDb' => [
                        'connectionName' => 'eb',
                        'dbName' => 'eb',
                        'collectionName' => 'operators',
                    ],
                ],
            ],
        ],
        'clients' => [
            'class' => [
                'name' => '\gear\modules\user\GUserModule',
                'components' => [
                    'userDb' => [
                        'connectionName' => 'eb',
                        'dbName' => 'eb',
                        'collectionName' => 'clients',
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'db' => [
            'class' => '\gear\components\db\mysql\GMySqlConnectionComponent',
            'host' => '127.0.0.1',
            'user' => '',
            'password' => '',
            'database' => '',
        ],
    ],
];
