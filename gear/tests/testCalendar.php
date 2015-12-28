<?php

require 'd:/usr/github/gear/Core.php';
use gear\Core;
try {
    \gear\Core::init([
        'modules' => [
            'app' => ['class' => '\gear\library\GApplication'],
        ]
    ]);
    \gear\Core::app()->process->setProcesses([
        'index' => function() {
            Core::h('calendar')->setFormat('d/m/Y');
            //echo Calendar::{(string)time()}() . "\n";
            $from = Calendar::{(string)time()}();
            $result = $from->diff(Calendar::{'2015-08-18'}());
        },
    ]);
}
catch(Exception $e) {
    die($e->getMessage());
}

\gear\Core::app()->run();
