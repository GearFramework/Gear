<?php

require 'd:/usr/github/gear/Core.php';
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
            Core::h('calendar')->setFormat('d/m/Y');
            echo Calendar::{(string)time()}() . "\n";
            foreach(Core::h('calendar')->getDatesOfWeek() as $date)
            {
                echo $date . "\n";
            }
            preg_match_all('/(\d+\s*\w+)/', '1 day 3 hours 10 minutes', $founds);
            print_r($founds);
        },
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

\gear\Core::app()->run();
