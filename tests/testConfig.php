<?php

require 'd:/usr/github/gear/Core.php';
try
{
    \gear\Core::init(
    [
        'preloads' =>
        [
            'components' =>
            [
                'configurator' =>
                [
                    'class' => '\gear\components\gear\configurator\GConfigurator',
                    '#autoload' => true,
                    'extensions' => ['sockets'],
                ]
            ],
        ],
        'modules' =>
        [
            'app' => ['class' => '\gear\library\GApplication'],
        ]
    ]);
}
catch(Exception $e)
{
    die($e->getMessage());
}

\gear\Core::c('configurator')->tests = [function()
{
    echo "First test\n";
    return true;
},
function()
{
    echo "Second test\n";
    return true;
}];
try
{
    \gear\Core::c('configurator')->test()->test(function($conf)
    {
        echo "Third test\n";
        $conf->e('Error: Invalid configuration');
        return true;
    });
}
catch(\Exception $e)
{
    echo $e->getMessage();
}
