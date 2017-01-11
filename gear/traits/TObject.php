<?php

namespace gear\traits;
use gear\Core;
use gear\interfaces\IObject;

/**
 * Трэйт для добавления объектам базовых свойств и методов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TObject
{
    /**
     * @var null|object владелец объекта
     */
    protected $_owner = null;

    /**
     * GObject constructor.
     *
     * @param array|\Closure $properties
     * @param null|object $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function __construct($properties = [], $owner = null)
    {
        $this->beforeConstruct($properties);
        if ($properties instanceof \Closure)
            $properties = $properties($this);
        if (!is_array($properties))
            $properties = [];
        if ($owner)
            $this->owner = $owner;
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
        $this->afterConstruct();
    }

    /**
     * Возвращает владельца объекта
     *
     * @return null|object
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * Установка владельца объекта
     *
     * @param object $owner
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setOwner($owner)
    {
        if (!is_object($owner))
            throw $this->exceptionObject('Owner must be a object');
        $this->_owner = $owner;
    }
}