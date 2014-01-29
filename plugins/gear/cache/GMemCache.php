<?php

namespace gear\plugins\gear\cache;
use gear\Core;
use gear\library\cache\GCache;

/** 
 * Плагин для Memcache
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 29.01.2014
 */
class GMemCache extends GCache
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    /**
     * Добавление значения в кэш
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function add($key, $value, $expire = 30)
    {
    }
    
    /**
     * Добавление значения или обновление существующего в 
     * кэше
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function set($key, $value, $expire = 30)
    {
    }
    
    /**
     * Получение значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function get($key, $unserialize = false)
    {
    }
    
    /**
     * Проверка на наличие в кэше значения под указанным ключём
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    public function exists($key)
    {
    }
    
    /**
     * Удаление значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
    }
    
    /**
     * Очистка кэша
     * 
     * @abstract
     * @access public
     * @return boolean
     */
    public function clear()
    {
    }
}
