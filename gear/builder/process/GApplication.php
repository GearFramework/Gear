<?php

namespace gear\builder\process;
use \gear\Core;
use \gear\models\GProcess;

class GApplication extends GProcess
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_instance = null;
    /* Public */
    public $defaultApi = 'createApplication';
    
    public function apiCreateApplication()
    {
        $params = array
        (
            'appName' => (Core::app()->request->get('name') ? $name = Core::app()->request->get('name') : 'Test'),
            'appNs' => strtolower($name),
            'appPath' => ($path = dirname(GEAR)),
            'appClass' => ucfirst($name),
        );
        $manifestFile = $path . str_replace('\\', '/', Core::app()->getNamespace()) . '/app.' . $name . '.manifest';
        if (!file_exists($manifestFile))
            $manifestFile = $path . str_replace('\\', '/', Core::app()->getNamespace()) . '/builder.app.manifest';
        $manifest = require $manifestFile;
        echo "+ Create application {$params['appName']}\n";
        return Core::c('builder')->createApplication($params, $manifest);
    }
    
    public function onBeforeExec($event)
    {
        if (!Core::app()->hasCli())
        {
            echo "Only cli-mode";
            return false;
        }
        return true;
    }
}