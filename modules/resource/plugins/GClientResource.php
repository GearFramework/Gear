<?php

namespace gear\modules\resource\plugins;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GEvent;
use \gear\library\GException;

class GClientResource extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function getResource($file)
    {
        if (!$this->inCache($file))
            $hash = $this->cache($file);
        else
            $hash = $this->getHash($file);
        return '?e=gear/resource/get&resource=' . $hash;
    }
}