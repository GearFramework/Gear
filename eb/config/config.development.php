<?php

return [
    'modules' => [
        'app' => [
            'class' => [
                'name' => '\eb\ElectroBunker',
                'components' => [
                    'controllers' => [
                        'defaultControllerName' => 'home',
                        'rewrite' => true,
                        'mapControllers' => [
                            'operators/auth' => '\eb\controllers\operators\AuthController',
                        ],
                    ],
                ],
            ],
        ],
        'resources' => [
            'class' => [
                'name' => '\gear\modules\resources\GResourcesModule',
                'plugins' => [
                    'js' => ['mappingFolder' => '/js'],
                    'css' => ['mappingFolder' => '/css'],
                ],
            ],
        ],
        'operators' => [
            'class' => [
                'name' => '\gear\modules\user\GUserModule',
                'components' => [
                    'userDb' => [
                        'connectionName' => 'db',
                        'dbName' => 'eb',
                        'collectionName' => 'operators',
                    ],
                    'session' => [
                        'connectionName' => 'db',
                        'dbName' => 'eb',
                        'collectionName' => 'operatorSessions',
                    ]
                ],
                'authController' => 'operators/auth',
                'loginController' => 'operators/auth',
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
                    'session' => [
                        'connectionName' => 'eb',
                        'dbName' => 'eb',
                        'collectionName' => 'clientSessions',
                    ]
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
