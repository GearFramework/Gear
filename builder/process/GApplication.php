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
            'class' => 'G' . ucfirst($name),
        );
        if (!($manifestFile = Core::app()->request->get('manifest')))
            $manifestFile = $path . str_replace('\\', '/', Core::app()->getNamespace()) . '/builder.app.manifest';
        echo $manifestFile;
    }
}