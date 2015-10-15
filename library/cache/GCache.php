<?php

namespace gear\library\cache;
use gear\library\GPlugin;

/** 
 * Кэш 
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 28.01.2014
 * @php 5.4.x
 * @release 1.0.0
 */
abstract class GCache extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_cache = null;
    /* Public */
    
    /**
     * Добавление нового значения в кэш
     * 
     * @abstract
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    abstract public function add($key, $value, $expire = 30);

    /**
     * Добавление нового значения в кэш или обновление существующего
     * 
     * @abstract
     * @access public
     * @param string $key
     * @param mixed $value
     * @param integer $expire
     * @return boolean
     */
    abstract public function set($key, $value, $expire = 30);

    /**
     * Получение значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    abstract public function get($key);
    
    /**
     * Проверка на наличие в кэше значения под указанным ключём
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    abstract public function exists($key);

    /**
     * Удаление значения из кэша
     * 
     * @abstract
     * @access public
     * @param string $key
     * @return boolean
     */
    abstract public function remove($key);

    /**
     * Очистка кэша
     * 
     * @abstract
     * @access public
     * @return boolean
     */
    abstract public function clear();
}
