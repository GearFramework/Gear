<?php

namespace gear\modules\resource\plugins;
use gear\Core;
use gear\library\cache\GCache;

/** 
 * Кэш для работы с ресурсами 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
class GResourceCache extends GCache
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $store = 'temp\resources';
    
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
    
    public function get($key, $unserialize = false)
    {
        if ($file = $this->exists($key))
        {
            $content = @file_get_contents($file);
            if ($content && $unserialize)
                $content = unserialize($content);
            return $content;
        }
        return null;
    }
    
    public function remove($key)
    {
        return ($file = $this->exists($key)) ? @unlink($file) : false;
    }
    
    public function clear()
    {
        $path = Core::resolvePath($this->store);
        foreach(scandir($path) as $file)
            @unlink($path . '/' . $file);
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