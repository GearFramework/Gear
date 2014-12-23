<?php

namespace gear;

use gear\Core;

class GServicesContainer
{
    /* Const */
    /* Private */
    private $_services = [];
    /* Protected */
    /* Public */
    
    public function registerService($serviceLocation, array $service)
    {
        $this->_services[$serviceLocation] = $service;
        if (isset($this->_services[$serviceLocation]['autoload']) &&
            $this->_services[$serviceLocation]['autoload'] === true)
        {
            $this->getRegisteredService($serviceLocation);
        }
        return $this;
    }
    
    public function isRegisteredService($service) 
    { 
        return isset($this->_services[$service]); 
    }
    
    public function installService($serviceLocation, $service)
    {
        if (is_array($service))
        {
            list($class, $config, $properties) = Core::getRecords($service);
            if (method_exists($class, 'install'))
                $service = $class::install($config, $properties);
            else
                $service = $this->_services[$serviceLocation] = new $class($properties);
        }
        $this->_services[$serviceLocation] = $service;
        return $this;
    }
    
    public function isInstalledService($service) 
    { 
        return isset($this->_services[$service]) && is_object($this->_services[$service]); 
    }
    
    public function uninstallService($service)
    {
        if (isset($this->_services[$service]))
        {
            $this->_services[$service]->event('onUninstall');
            unset($this->_services[$service]);
        }
        return $this;
    }
    
    public function getRegisteredService($service, $clone = false)
    {
        if (!isset($this->_services[$service]))
            Core::e('Service :service not registered', ['service' => $service]);
        if (!is_object($this->_services[$service]))
        {
            list($class, $config, $properties) = Core::getRecords($this->_services[$service]);
            if (method_exists($class, 'install'))
                return $this->_services[$service] = $class::install($config, $properties);
            else
                return $this->_services[$service] = new $class($properties);
        }
        return $clone ? clone $this->_services[$service] : $this->_services[$service];
    }
}
