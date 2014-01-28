<?php

namespace \gear\modules\resource\plugins;
use gear\Core;
use gear\library\cache\GCache;

class GResourceCache extends GCache
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $store = 'temp';
    
    public function add($key, $value, $expire = 30)
    {
        if ($this->exists($key))
            return false;
        return @file_put_contents($file, is_array($value) || is_object($value) ? serialize($value) : $value);
    }
    
    public function set($key, $value, $expire = 30)
    {
        $file = Core::resolvePath($this->store . '/' . $key);
        return @file_put_contents($file, is_array($value) || is_object($value) ? serialize($value) : $value);
    }
    
    
    
    public function exists($key)
    {
        return file_exists($file = Core::resolvePath($this->store . '/' . $key)) ? $file : false;
    }
    
    public function onConstructed()
    {
        parent::onConstructed();
    }
}