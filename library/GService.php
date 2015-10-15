<?php

namespace gear\library;

use gear\Core;
use gear\library\GObject;
use gear\library\GException;
use gear\interfaces\IService;

/** 
 * Класс сервисов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 25.12.2014
 * @php 5.4.x
 * @release 1.0.0
 */
abstract class GService extends GObject implements IService
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    protected static $_init = false;
    protected $_name = null;
    /* Public */
    
    /**
     * Установка компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @param array $properties
     * @return GService
     */
    public static function install($config, array $properties = [])
    {
        if (static::$_init === false)
            static::init($config);
        $args = func_get_args();
        array_shift($args);
        $instance = call_user_func_array([get_called_class(), 'it'], $args);
        $instance->trigger('onInstalled');
        return $instance;
    }
    
    /**
     * Конфигурирование класса компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @return bool
     * @throws GException
     */
    public static function init($config)
    {
        if (is_string($config))
            $config = require(Core::resolvePath($config));
        if (!is_array($config))
            throw static::exceptionService('Incorrect configuration of service');
        static::$_config = array_replace_recursive(static::$_config, $config);
        list(,,static::$_config) = Core::getRecords(static::$_config);
        if (isset(static::$_config['components']))
        {
            foreach(static::$_config['components'] as $componentName => $component)
                Core::services()->registerService(get_called_class() . '.components.' . $componentName, $component);
        }
        return static::$_init = true;
    }
    
    /**
     * Получение экземпляра компонента
     * 
     * @access public
     * @static
     * @param array $properties
     * @param null|object $owner
     * @return GComponent
     */
    public static function it(array $properties = [], $owner = null)
    {
        return new static($properties, $owner);
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

    /**
     * Возвращает имя сервиса
     *
     * @access public
     * @return string
     */
    public function getName() { return $this->_name; }

    /**
     * Устанавливает имя сервиса
     *
     * @access public
     * @param string $nameService
     * @return $this
     */
    public function setName($nameService)
    {
        $this->_name = $nameService;
        return $this;
    }

    /**
     * Возвращает имя сервиса
     *
     * @access public
     * @return string
     */
    public function name() { return $this->getName(); }
}
