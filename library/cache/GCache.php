<?php

namespace gear\library\cache;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GException;

/** 
 * Кэш 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 28.01.2014
 */
abstract class GCache extends GPlugin
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