<?php

require 'd:/usr/github/gear/Core.php';
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
        'index' => function() { echo "Index process\n"; },
        'foo' => function() { return (new \GTestProcess())->foo(); },
        'test' => ['class' => '\GTestProcess'],
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

class GTestProcess extends \gear\models\GProcess
{
    public function apiIndex()
    {
        echo "Class process " . get_class($this) . "\n";
    }

    public function foo()
    {
        echo "Foo process\n";
    }
}

\gear\Core::app()->run();
