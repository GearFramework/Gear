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
            'name' => (Core::app()->request->get('name') ? $name = Core::app()->request->get('name') : 'Test'),
            'ns' => strtolower($name),
            'path' => ($path = dirname(GEAR)),
            'class' => ucfirst($name),
        );
        $manifestFile = $path . str_replace('\\', '/', Core::app()->getNamespace()) . '/app.' . $name . '.manifest';
        if (!file_exists($manifestFile))
            $manifestFile = $path . str_replace('\\', '/', Core::app()->getNamespace()) . '/builder.app.manifest';
        $manifest = json_decode(file_get_contents($manifestFile));
        echo "> Create application {$params['name']}\n";
        echo "> Application namespace {$params['ns']}\n";
        echo "> Class {$params['ns']}\\{$params['class']}\n";
        $appPath = $params['path'] . '/' . $params['name'];
        echo "> Create app folder $appPath\n";
        if (file_exists($appPath))
        {
            echo "> Application {$params['name']} already exists\n> Terminate\n";
            return false;
        }
        mkdir($appPath);
        foreach($manifest->folders as $folder)
        {
            $folder = $appPath . '/' . $folder;
            echo "> Create app folder $folder\n";
            mkdir($folder);
        }
        $classAppFile = $appPath . '/' . $params['class'] . '.php';
        echo "> Create file of class $classAppFile\n";
        Core::c('templater')->createFile(GEAR . '/builder/templates/app.php.tpl', $classAppFile, array
        (
            'nsApp' => $params['ns'],
            'classApp' => $params['class'],
        ));
        $classProcess = $params['ns'] . '\\process\\GIndex';
        $fileProcess = $appPath . '/process/GIndex.php';
        echo "> Create file of process $fileProcess\n";
        echo "> Create class of process $classProcess\n";
        Core::c('templater')->createFile(GEAR . '/builder/templates/process.php.tpl', $fileProcess, array
        (
            'nsProcess' => $params['ns'] . '\\process',
        ));
        $indexFile = $manifest->documentRoot . '/index.php';
        echo "Create index $indexFile\n";
        Core::c('templater')->createFile(GEAR . '/builder/templates/index.php.tpl', $indexFile, array
        (
            'gear' => GEAR,
        ));
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