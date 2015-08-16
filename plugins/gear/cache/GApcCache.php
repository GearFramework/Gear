<?php

namespace gear\plugins\gear\cache;
use gear\Core;
use gear\library\cache\GCache;

/** 
 * Плагин для APC-кэша
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 29.01.2014
 * @php 5.3.x
 * @release 1.0.0
 */
class GApcCache extends GCache
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
    public function add($key, $value = null, $expire = 30)
    {
        if (is_array($key))
        {
            if ($value !== null)
                $expire = (int)$value;
            apc_add($key, null, $expire);
        }
        else
            return apc_add($key, $value, $expire);
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
        if (is_array($key))
        {
            if ($value !== null)
                $expire = (int)$value;
            apc_store($key, null, $expire);
        }
        else
            return apc_store($key, $value, $expire);
    }
    
    /**
     * Получение значения из кэша
     * 
     * @access public
     * @param string|array $key
     * @param boolean|closure $unserialize
     * @return mixed
     */
    public function get($key, $unserialize = false)
    {
        if (is_array($key))
        {
            $keys = array();
            foreach($key as $k => $us)
                if (!is_bool($us) && !is_callable($us))
                    $keys[$us] = $unserialize;
                else
                    $keys[$k] = $us;
            $result = apc_fetch(array_keys($keys));
            foreach($result as $k => $v)
                if ($keys[$k])
                    $result[$k] = is_callable($keys[$k]) ? call_user_func($keys[$k], $v) : unserialize($v);
            return $result;
        }
        return apc_fetch($key);
    }
    
    /**
     * Проверка на наличие в кэше значения под указанным ключём
     * 
     * @access public
     * @param string|array $key
     * @return boolean
     */
    public function exists($key)
    {
        return apc_exists($key);
    }
    
    /**
     * Удаление значения из кэша
     * 
     * @access public
     * @param string|array $key
     * @return boolean
     */
    public function remove($key)
    {
        if (is_array($key))
        {
            $size = $result = count($key);
            foreach($key as $k)
                $result = apc_delete($k) ? $result - 1 : $result;
            return !$result ? true : ($result === $size ? false : $result);
        }
        return apc_delete($key);
    }
    
    /**
     * Очистка кэша
     * 
     * @access public
     * @return boolean
     */
    public function clear() { return apc_clear_cache(); }
    
    /**
     * Увеличичвает значение в кэше на $step
     * 
     * @access public
     * @param string $key
     * @param integer $step
     * @return boolean
     */
    public function inc($key, $step = 1) { return apc_inc($key, $step); }
    
    /**
     * Уменьшает значение в кэше на $step
     * 
     * @access public
     * @param string $key
     * @param integer $step
     * @return boolean
     */
    public function dec($key, $step = 1) { return apc_dec($key, $step); }
}
