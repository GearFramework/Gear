<?php

require __DIR__ . '/../Core.php';
use gear\Core;
try
{
    Core::init(
    [
        'modules' =>
        [
            'app' => ['class' => '\gear\library\GApplication'],
        ],
        'components' =>
        [
            'ftp' => ['class' => '\gear\components\gear\ftp\GFtp'],
        ],
    ]);
    Core::app()->process->setProcesses(
    [
        'index' => function()
        {
            Core::c('loader')->setAlias(get_class(Core::c('ftp')), 'Ftp');
            try
            {
                Ftp::{'ftp://test:1qaz3edc5tgb@localhost:2190/test'}(['timeout' => 100, 'pasv' => true]);
            }
            catch(\Exception $e)
            {
                echo $e->getMessage() . "\n";
            }
        },
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

Core::app()->run();
