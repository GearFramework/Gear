<?php

namespace gear\library;

use \gear\Core;
use \gear\library\GService;
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
abstract class GModule extends GService implements IModule
{
    /* Traits */
    use \gear\traits\TNamedService;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    protected static $_init = false;
    protected $_components = [];
    protected $_nameService = null;
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
    public static function install($config, array $properties = [])
    {
        if (static::$_init === false)
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
            static::e('Invalid configuration');
        if (isset($config['#include']))
        {
            $include = $config['#include'];
            unset($config['#include']);
            $include = require(Core::resolvePath($include));
            $config = array_replace_recursive($config, $include);
        }
        static::$_config = array_replace_recursive(static::$_config, $config);
        if (isset(static::$_config['components']))
        {
            foreach(static::$_config['components'] as $componentName => $component)
            {
                Core::services()->registerService(self::class . '.components.' . $componentName, $component);
            }
        }
    }
    
    /**
     * Получение экземпляра модуля
     * 
     * @access public
     * @static
     * @param array $properties
     * @return GComponent
     */
    public static function it(array $properties = [])
    {
        return new static($properties);
    }
    
    public function __get($name)
    {
        return $this->isComponentRegistered($name) ? $this->c($name) : parent::__get($name);
    }
    
    /**
     * Получение компонента, зарегистрированного модулем
     * 
     * @access public
     * @param string $name
     * @param boolean $instance
     * @return GComponent
     */
    public function c($name, $instance = false)
    {
        $location = self::class . '.components.' . $name;
        if (!Core::services()->isRegisteredService($location))
                $this->e('Component :componentName is not registered', array('componentName' => $name));
        return Core::services()->getRegisteredService($location, $instance);
/*        if (!isset($this->_components[$name]))
        {
            if (!($component = $this->isComponentRegistered($name)))
                $this->e('Компонент модуля ":componentName" не зарегистрирован', array('componentName' => $name));
            list($class, $config, $properties) = Core::getRecords($component);
            return $this->_components[$name] = $class::install($config, $properties, $this);
        }
        return $instance ? clone $this->_components[$name] : $this->_components[$name];*/
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
        return Core::services()->isRegisteredService(self::class . '.components.' . $name);
//        return isset(static::$_config['components'][$name]) ? static::$_config['components'][$name] : false;
    }
    
    /**
     * Регистрация компонента
     * 
     * @access public
     * @param string $name
     * @param array $component
     * @return $this
     */
    public function registerComponent($name, $component)
    {
        Core::services()->registerService(self::class . '.components.' . $name, $component);
//        static::$_config['components'][$name] = $component;
        return $this;
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
