<?php

define('GEAR', '/usr/share/gearteam/gear/gear'); // Redefine this constant on your project
include GEAR . '/Core.php';
\gear\Core::init([
        'modules' => [
            'app' => ['class' => '\demo\hello\Hello'],
        ],
        'components' => [

        ],
    ],
    \gear\Core::MODE_PRODUCTION
);
\gear\Core::app()->run();
