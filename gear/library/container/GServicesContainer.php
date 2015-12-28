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
 * @php 5.4.x or higher
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
    public function registerService($serviceLocation, array $service) {
        Core::syslog(get_class($this) . ' -> Register service ' . $serviceLocation . '[' . __LINE__ . ']');
        $this->_services[$serviceLocation] = $service;
        if (isset($this->_services[$serviceLocation]['#autoload']) &&
            $this->_services[$serviceLocation]['#autoload'] === true) {
            Core::syslog(get_class($this) . ' -> Autoload service ' . $serviceLocation . '[' . __LINE__ . ']');
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
    public function isRegisteredService($serviceLocation) {
        return isset($this->_services[$serviceLocation]);
    }
    
    /**
     * Инсталляция сервиса (создание инстанса)
     * 
     * @access public
     * @param string $serviceLocation
     * @param array|object $service
     * @return object
     */
    public function installService($serviceLocation, $service) {
        Core::syslog(get_class($this) . ' -> Install service ' . $serviceLocation . '[' . __LINE__ . ']');
        if (is_array($service)) {
            list($class, $config, $properties) = Core::getRecords($service);
            if (method_exists($class, 'install')) {
                $service = $class::install($config, $properties);
            }
            else if (method_exists($class, 'it')) {
                if (method_exists($class, 'init'))
                    $class::init($service);
                $service = $class::it($properties);
            }
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
    public function isInstalledService($serviceLocation) {
        return isset($this->_services[$serviceLocation]) && is_object($this->_services[$serviceLocation]);
    }
    
    /**
     * Деинсталляция сервиса
     * 
     * @access public
     * @param string $serviceLocation
     * @return $this
     */
    public function uninstallService($serviceLocation) {
        if (isset($this->_services[$serviceLocation])) {
            Core::syslog(get_class($this) . ' -> Uninstall service ' . $serviceLocation . '[' . __LINE__ . ']');
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
    public function getRegisteredService($serviceLocation, $clone = false, $owner = null) {
        Core::syslog(get_class($this) . ' -> Get registered service ' . $serviceLocation . ($clone ? " as clone" : "") . ' [' . __LINE__ . ']');
        if (!isset($this->_services[$serviceLocation])) {
            Core::syslog(get_class($this) . ' -> Service ' . $serviceLocation . ' not found[' . __LINE__ . ']');
            throw Core::exceptinServiceNotRegistered(['serviceName' => $serviceLocation]);
        }
        if (!is_object($this->_services[$serviceLocation])) {
            Core::syslog(get_class($this) . ' -> Create instance of service ' . $serviceLocation . ' [' . __LINE__ . ']');
            list($class, $config, $properties) = Core::getRecords($this->_services[$serviceLocation]);
            Core::syslog(get_class($this) . ' -> Class service ' . $serviceLocation . ' is ' . $class . ' [' . __LINE__ . ']');
            if (method_exists($class, 'install'))
                $this->_services[$serviceLocation] = $class::install($config, $properties, $owner);
            else if (method_exists($class, 'it'))
                $this->_services[$serviceLocation] = $class::it($properties, $owner);
            else
                $this->_services[$serviceLocation] = new $class($properties, $owner);
            Core::syslog(get_class($this) . ' -> Create instance ' . $serviceLocation . ' ' . (is_object($this->_services[$serviceLocation]) ? "[DONE]" : "[ERROR]") . ' [' . __LINE__ . ']');
        }
        return $clone ? clone $this->_services[$serviceLocation] : $this->_services[$serviceLocation];
    }
}
