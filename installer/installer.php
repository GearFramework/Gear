<?php

require_once __DIR__ . '/../Core.php';

use gear\Core;

Core::init(
[
    'modules' =>
    [
        'app' =>
        [
            'class' =>
            [
                'name' => '\gear\installer\InstallerApplication',
                'components' =>
                [
                    'process' => ['defaultProcess' => 'usage'],
                ],
            ],
        ],
    ],
]);
Core::app()->run();
