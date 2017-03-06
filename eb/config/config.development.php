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
                            'operators/login' => ['\eb\controllers\operators\AuthController', 'login'],
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
                    'session' => [
                        'connectionName' => 'eb',
                        'dbName' => 'eb',
                        'collectionName' => 'operatorSessions',
                    ]
                ],
                'loginController' => 'operators/login',
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
