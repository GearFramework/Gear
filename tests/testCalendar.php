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
            //echo Calendar::{(string)time()}() . "\n";
            $from = Calendar::{(string)time()}();
            $to = Calendar::{'2015-08-18'}();
            foreach(Core::h('calendar')->getRange($from, $to, '1 week') as $date)
            {
                echo $date . "\n";
            }
        },
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

\gear\Core::app()->run();
