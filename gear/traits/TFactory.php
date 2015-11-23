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
     * @param array|\Closure $properties
     * @return object
     */
    public function factory($properties = array())
    {
        if ($this->event('onBeforeFactory', new GEvent(array('sender' => $this)), $properties, $this->getFactory()))
        {
            if ($properties instanceof \Closure)
                $properties = $properties($this->getFactory());
            $properties = array_merge
            (
                $this->getFactory(),
                array('owner' => $this),
                $properties
            );
            list($class, $config, $properties) = Core::getRecords($properties);
            if (method_exists($class, 'init'))
                $class::init($config);
            $object = method_exists($class, 'it') ? $class::it($properties) : new $class($properties);
            $this->event('onAfterFactory', new GEvent(array('sender' => $this)), $object);
            return $object;
        }
        $this->e('Error on factoring process');
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
