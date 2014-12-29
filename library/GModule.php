<?php

namespace gear\library;

use gear\Core;
use gear\library\GService;
use gear\library\GException;
use gear\interfaces\IModule;

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
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    protected static $_init = false;
    /* Public */

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
     * @return IComponent
     */
    public function c($name, $instance = false)
    {
        $location = static::class . '.components.' . $name;
        if (!Core::services()->isRegisteredService($location))
                $this->e('Component :componentName is not registered', array('componentName' => $name));
        return Core::services()->getRegisteredService($location, $instance);
    }
    
    /**
     * Возвращает запись о компоненте модуля, иначе false если компонент
     * с указанным именем не зарегистрирован
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function isComponentRegistered($name)
    {
        return Core::services()->isRegisteredService(static::class . '.components.' . $name);
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
        Core::services()->registerService(static::class . '.components.' . $name, $component);
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
