<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;
use \gear\interfaces\IComponent;

/** 
 * Класс компонентов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
abstract class GComponent extends GObject implements IComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected $_owner = null;
    /* Public */
    
    /**
     * Установка компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @param array $properties
     * @param null|object $owner
     * @return GComponent
     */
    public static function install($config, array $properties = array(), $owner = null)
    {
        if (static::$_init === false)
            static::init($config);
        $instance = static::it($properties, $owner);
        $instance->event('onInstalled');
        return $instance;
    }
    
    /**
     * Конфигурирование класса компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @return void
     */
    public static function init($config)
    {
        if (is_string($config))
            $config = require(Core::resolvePath($config));
        if (!is_array($config))
            static::e('Incorrect configuration');
        static::$_config = array_replace_recursive(static::$_config, $config);
    }
    
    /**
     * Получение экхемпляра компонента
     * 
     * @access public
     * @static
     * @param array $properties
     * @param nulll|object $owner
     * @return GComponent
     */
    public static function it(array $properties = array(), $owner = null)
    {
        if ($owner)
            $properties['owner'] = $owner;
        return new static($properties);
    }

    /**
     * Возвращает true, если компонент может быть перегружен, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isOverride()
    {
        return isset($this->_properties['override']) && (bool)$this->_properties['override'] === true;
    }
}

/** 
 * Исключения компонента
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class ComponentException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
