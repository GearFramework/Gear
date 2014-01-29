<?php

namespace gear\modules\resource\plugins;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GEvent;
use \gear\library\GException;

abstract class GClientResource extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $resources = null;
    
    public function resolvePath($file)
    {
        if ($file[0] !== '\\')
            $file = $this->getOwner()->storage . '\\' . ($this->resources ? $this->resources . '\\' : '') . $file;
        $file = Core::resolvePath($file);
    }
    
    public function publicate($file, $render = false)
    {
        $file = $this->resolvePath($file);
        $hash = $this->getHash($file);
        if (!$this->inCache($hash))
            $hash = $this->cache($file, array('render' => $render));
        return $hash ? '?e=gear/resource/get&resource=' . $hash . '&contentType=' . $this->resources : '';
    }
    
    public function get($hash)
    {
        if ($this->inCache($hash))
        {
            $resource = $this->getOwner()->cache->get($hash, true);
            if (is_array($resource))
            {
                return $resource['render'] 
                       ? $this->view->render($resource['resource'], array(), true)
                       : file_get_contents($resource['resource']);
            }
        }
        return null;
    }
    
    abstract public function getContentType();
}