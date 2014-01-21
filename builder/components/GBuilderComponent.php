<?php

namespace gear\builder\components;

use \gear\Core;
use \gear\library\GComponent;

class GTemplaterComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    /* Public */
    
    public function createFile($templateFile, $targetFile, array $params = array())
    {
        $content = file_get_contents($templateFile);
        foreach($params as $paramName => $paramValue)
            $content = str_replace("{:$paramName}", $paramValue, $content);
        file_put_contents($targetFile, $content);
    }
}