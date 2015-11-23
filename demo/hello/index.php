<?php

define('GEAR', '/usr/share/gearteam/gear'); // Redefine this constant on your project
include GEAR . '/Core.php';
\gear\Core::init([
        'modules' => [
            'app' => ['class' => '\demo\hello\Hello'],
        ],
    ],
    \gear\Core::MODE_PRODUCTION
);
\gear\Core::app()->run();
