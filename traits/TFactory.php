<?php

namespace gear\traits;
use gear\Core;
use gear\library\GEvent;

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
        $this->event('onBeforeFactory', new GEvent(array('sender' => $this)), $properties, $this->factory);
        $properties = array_merge
        (
            $propertyCallback ? $propertyCallback($this->factory, $properties) : $this->factory,
            array('owner' => $this),
            $properties
        );
        list($class, $config, $properties) = Core::getRecords($properties);
        if (method_exists($class, 'init'))
            $class::init($config);
        $object = method_exists($class, 'it') ? $class::it($properties) : new $class($properties);
        $this->event('onAfterFactory', new GEvent(array('sender' => $this)), $object);
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
