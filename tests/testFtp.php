<?php

require __DIR__ . '/../Core.php';
use gear\Core;
try
{
    \gear\Core::init(
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
    \gear\Core::app()->process->setProcesses(
    [
        'index' => function()
        {
            Core::c('loader')->setAlias(get_class(Core::c('ftp')), 'Ftp');
            try
            {
                Ftp::{'ftp://localhost:21'}(['timeout' => 100, 'pasv' => true]);
/*                Core::c('ftp')->connect('ftp://username:password@sld.domain.tld/path1/path2/');
                Core::c('ftp')->connect('ftp://username:password@sld.domain.tld:22/path1/path2/');
                Core::c('ftp')->connect('ftp://username:password@sld.domain.tld');
                Core::c('ftp')->connect('ftp://username:password@sld.domain.tld:22');
                Core::c('ftp')->connect('ftp://sld.domain.tld');
                Core::c('ftp')->connect('ftp://sld.domain.tld:22');
                Core::c('ftp')->connect('username:password@sld.domain.tld/path1/path2/');
                Core::c('ftp')->connect('username:password@sld.domain.tld:22/path1/path2/');
                Core::c('ftp')->connect('username:password@sld.domain.tld');
                Core::c('ftp')->connect('username:password@sld.domain.tld:22');
                Core::c('ftp')->connect('sld.domain.tld/path1/path2/');
                Core::c('ftp')->connect('sld.domain.tld:22/path1/path2/');
                Core::c('ftp')->connect('sld.domain.tld');
                Core::c('ftp')->connect('sld.domain.tld:22');*/
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

\gear\Core::app()->run();
