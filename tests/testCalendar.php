<?php

require 'c:/usr/github/gear/Core.php';
use gear\Core;
try
{
    \gear\Core::init(
    [
        'modules' =>
        [
            'app' => ['class' => '\gear\library\GApplication'],
        ]
    ]);
    \gear\Core::app()->process->setProcesses(
    [
        'index' => function()
        {
            echo Core::h('calendar')->setFormat('d/m/Y')->getLastDateOfYear();
        },
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

\gear\Core::app()->run();
