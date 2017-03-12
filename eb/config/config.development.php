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
                        'factory' => [
                            'class' => '\gear\modules\user\models\GSession',
                            'validators' => [
                                'sessionTimeLife' => [
                                    [
                                        'class' => '\gear\validators\GSessionValidator',
                                        'timeLife' => 900,
                                    ], 'validateTimeLife'
                                ],
                            ],
                        ],
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
        'db' => include(__DIR__ . '/config.db.php'),
    ],
];
