<?php

require 'd:/usr/github/gear/Core.php';
try
{
    \gear\Core::init(
    [
        'preloads' =>
        [
            'components' =>
            [
                'syslog' =>
                [
                    'class' =>
                    [
                        'name' => '\gear\components\gear\syslog\GSyslog',
                        'plugins' =>
                        [
                            'fileLog' =>
                            [
                                'location' => '\gear\logs',
                                'maxLogFileSize' => 100,
                                'overheadFileSize' => 'rotate',
                                'maxRotateFiles' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'modules' =>
        [
            'app' =>
            [
                'class' =>
                [
                    'name' => '\gear\library\GApplication',
                ],
            ],
        ]
    ]);
    \gear\Core::app()->process->setProcesses(
    [
        'index' => function()
        {
            \gear\Core::syslog(\gear\Core::WARNING, 'Test system logs');
            \gear\Core::app()->setNamespace('gear')->log(\gear\Core::WARNING, 'Test application logs');
        },
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

\gear\Core::app()->run();
