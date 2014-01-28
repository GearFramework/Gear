<?php

namespace \gear\library\cache;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;

abstract class GCache extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_cache = null;
    /* Public */
    
    abstract public function add($key, $value, $expire = 30);
    abstract public function set($key, $value, $expire = 30);
    abstract public function get($key);
    abstract public function exists($key);
    abstract public function remove($key);
    abstract public function clear();
}

class CacheException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}