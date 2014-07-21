<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;
use \gear\interfaces\IModule;

/** 
 * Класс модулей
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
abstract class GModule extends GObject implements IModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected $_components = array();
    /* Public */
    
    /**
     * Установка модуля
     * 
     * @access public
     * @static
     * @param string|array $config
     * @param array $properties
     * @return GComponent
     */
    public static function install($config, array $properties = array())
    {
        static::init($config);
        $instance = static::it($properties);
        $instance->event('onInstalled');
        return $instance;
    }
    
    /**
     * Конфигурирование класса модуля
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
            static::e('Некорректная конфигурация');
        static::$_config = array_replace_recursive(static::$_config, $config);
    }
    
    /**
     * Получение экземпляра модуля
     * 
     * @access public
     * @static
     * @param array $properties
     * @return GComponent
     */
    public static function it(array $properties = array())
    {
        return new static($properties);
    }
    
    public function __get($name)
    {
        return $this->isComponentRegistered($name) ? $this->c($name) : parent::__get($name);
    }
    
    /**
     * Получение компонента, харегистрированного модулем
     * 
     * @access public
     * @param string $name
     * @return GComponent
     */
    public function c($name)
    {
        if (!isset($this->_components[$name]))
        {
            if (!($component = $this->isComponentRegistered($name)))
                $this->e('Компонент модуля ":componentName" не зарегистрирован', array('componentName' => $name));
            list($class, $config, $properties) = Core::getRecords($component);
            $this->_components[$name] = $class::install($config, $properties, $this);
        }
        return $this->_components[$name];
    }
    
    /**
     * Возвращает запись о компоненте модуля, иначе false если компонент
     * с указанным именем не зарегистрирован
     * 
     * @access public
     * @param string $name
     * @return array|false
     */
    public function isComponentRegistered($name)
    {
        return isset(static::$_config['components'][$name]) ? static::$_config['components'][$name] : false;
    }
    
    public function registerComponent($name, $component)
    {
        static::$_config['components'][$name] = $component;
    }

    /**
     * Возвращает true, если модуль может быть перегружен, иначе false
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
 * Исключения модуля
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class ModuleException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
