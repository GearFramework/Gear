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
    
    /**
     * Добавление информции о новом ресурсе в файловый кэш
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function add($key, $value, $expire = 30)
    {
        if ($this->exists($key))
            return false;
        return @file_put_contents($file, is_array($value) || is_object($value) ? serialize($value) : $value);
    }
    
    /**
     * Добавление информции о новом ресурсе или обновление существующего в 
     * файловом кэше
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    public function set($key, $value, $expire = 30)
    {
        $file = Core::resolvePath($this->store . '/' . $key);
        return @file_put_contents($file, is_array($value) || is_object($value) ? serialize($value) : $value);
    }
    
    /**
     * Получение информации о ресурсе из кэша
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
        if ($file = $this->exists($key))
        {
            $content = @file_get_contents($file);
            if ($content && $unserialize)
                $content = unserialize($content);
            return $content;
        }
        return null;
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
        return file_exists($file = Core::resolvePath($this->store . '/' . $key)) ? $file : false;
    }
    
    /**
     * Удаление информации о ресурсе из файлового кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
        return ($file = $this->exists($key)) ? @unlink($file) : false;
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
        $path = Core::resolvePath($this->store);
        foreach(scandir($path) as $file)
            @unlink($path . '/' . $file);
    }
}
