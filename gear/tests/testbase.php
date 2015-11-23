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
    echo "Test -> Set processes [" . __LINE__ . "]\n";
    \gear\Core::app()->process->setProcesses(
    [
        'index' => function()
        {
            echo "Index process\n";
        },
        'foo' => function() { return (new \GTestProcess())->foo(); },
        'test' => ['class' => '\GTestProcess'],
    ]);
    echo "Test -> Done set processes [" . __LINE__ . "]\n";
}
catch(Exception $e)
{
    die($e->getMessage() . "\n" . $e->getFile() . "[" . $e->getLine() . "]\n" . $e->getTraceAsString());
}

class GTestProcess extends \gear\models\GProcess
{
    public function apiIndex($a = 0, $b)
    {
        echo "Class process " . get_class($this) . "\n";
        echo "Get parametr a = $a\n";
    }

    public function foo()
    {
        echo "Foo process\n";
    }
}

try
{
    \gear\Core::app()->run();
}
catch(Exception $e)
{
    die($e->getMessage() . "\n" . $e->getFile() . "[" . $e->getLine() . "]\n" . $e->getTraceAsString());
}
