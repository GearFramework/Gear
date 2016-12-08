<?php

define('GEAR', dirname(__DIR__));

require_once GEAR . '/Core.php';

\gear\Core::init(['modules' => [
    'app' => ['class' => '\demo\hello\Hello'],
]]);
