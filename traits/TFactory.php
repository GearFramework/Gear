<?php

namespace gear\traits;
use gear\Core;

/**
 * Трейт фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.05.2015
 * @php 5.3.x
 */
trait TFactory
{
    /**
     * Создание объекта
     *
     * @access public
     * @param array $properties
     * @param \Closure $propertyCallback
     * @return object
     */
    public function factory(array $properties = array(), \Closure $propertyCallback = null)
    {
        $properties = array_merge
        (
            $propertyCallback ? $propertyCallback($this->_factory, $properties) : $this->_factory,
            array('owner' => $this),
            $properties
        );
        list($class, $config, $properties) = Core::getRecords($properties);
        if (method_exists($class, 'init'))
            $class::init($config);
        return method_exists($class, 'it') ? $class::it($properties) : new $class($properties);
    }

    /**
     * Установка параметров создаваемых объектов
     *
     * @access public
     * @param array $factory
     */
    public function setFactory(array $factory) { $this->_factory = $factory; }

    /**
     * Получение параметров создаваемых объектов
     *
     * @access public
     * @return array
     */
    public function getFactory() { return $this->_factory; }
}
