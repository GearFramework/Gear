<?php

namespace gear\library\container;

use gear\Core;

/**
 * Менеджер сервисов (управление модулями, компонентами, плагинами)
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 23.12.2014
 * @php 5.4.x
 * @release 1.0.0
 */
class GServicesContainer
{
    /* Traits */
    /* Const */
    /* Private */
    private $_services = [];
    /* Protected */
    /* Public */
    
    /**
     * Регистрация сервиса
     * 
     * @access public
     * @param string $serviceLocation
     * @param array $service
     * @return $this
     */
    public function registerService($serviceLocation, array $service)
    {
        $this->_services[$serviceLocation] = $service;
        if (isset($this->_services[$serviceLocation]['#autoload']) &&
            $this->_services[$serviceLocation]['#autoload'] === true)
        {
            unset($this->_services[$serviceLocation]['#autoload']);
            $this->getRegisteredService($serviceLocation);
        }
        return $this;
    }
    
    /**
     * Возвращает true если указанный сервис зарегисттрирован, иначе - false 
     * 
     * @access public
     * @param string $serviceLocation
     * @return boolean
     */
    public function isRegisteredService($serviceLocation) { return isset($this->_services[$serviceLocation]); }
    
    /**
     * Инсталляция сервиса (создание инстанса)
     * 
     * @access public
     * @param string $serviceLocation
     * @param array|object $service
     * @return object
     */
    public function installService($serviceLocation, $service)
    {
        if (is_array($service))
        {
            list($class, $config, $properties) = Core::getRecords($service);
            if (method_exists($class, 'install'))
                $service = $class::install($config, $properties);
            else
            if (method_exists($class, 'it'))
                $service = $class::it($properties);
            else
                $service = new $class($properties);
        }
        return $this->_services[$serviceLocation] = $service;
    }
    
    /**
     * Возвращает true если сервис инсталлирован, иначе - false
     * 
     * @access public
     * @param string $serviceLocation
     * @return boolean
     */
    public function isInstalledService($serviceLocation) 
    {
        return isset($this->_services[$serviceLocation]) && is_object($this->_services[$serviceLocation]);
    }
    
    /**
     * Деинсталляция сервиса
     * 
     * @access public
     * @param string $serviceLocation
     * @return $this
     */
    public function uninstallService($serviceLocation)
    {
        if (isset($this->_services[$serviceLocation]))
        {
            $this->_services[$serviceLocation]->trigger('onUninstall');
            unset($this->_services[$serviceLocation]);
        }
        return $this;
    }
    
    /**
     * Возврашщает инстанс зарегистрированного сервиса. Если $clone установлен
     * в true, то возвращается его копия
     * 
     * @access
     * @param string $serviceLocation
     * @param boolean $clone
     * @return object
     */
    public function getRegisteredService($serviceLocation, $clone = false, $owner = null)
    {
        if (!isset($this->_services[$serviceLocation]))
            throw Core::exceptinServiceNotRegistered(['serviceName' => $serviceLocation]);
        if (!is_object($this->_services[$serviceLocation]))
        {
            list($class, $config, $properties) = Core::getRecords($this->_services[$serviceLocation]);
            if (method_exists($class, 'install'))
                return $this->_services[$serviceLocation] = $class::install($config, $properties, $owner);
            else
            if (method_exists($class, 'it'))
                return $this->_services[$serviceLocation] = $class::it($properties, $owner);
            else
                return $this->_services[$serviceLocation] = new $class($properties, $owner);
        }
        return $clone ? clone $this->_services[$serviceLocation] : $this->_services[$serviceLocation];
    }
}
