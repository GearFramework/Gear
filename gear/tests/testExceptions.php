<?php

require __DIR__ . '/../Core.php';
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
        'index' => ['class' => '\GTestProcess'],
    ]);
}
catch(Exception $e)
{
    die($e->getMessage() . " [ERROR]\n");
}

class GTestProcess extends \gear\models\GProcess
{
    public function apiIndex()
    {
    }
}

try
{
    \gear\Core::app()->run();
}
catch(Exception $e)
{
    die($e->getMessage() . " [ERROR]\n");
}
