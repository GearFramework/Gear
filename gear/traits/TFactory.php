<?php

namespace gear\traits;

use gear\Core;
use gear\library\GEvent;

/**
 * Трейт фабрики
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TFactory
{
    /**
     * Создание объекта
     *
     * @param array $record
     * @param null $owner
     * @return object
     * @since 0.0.1
     * @version 0.0.1
     */
    public function factory(array $record = [], $owner = null)
    {
        list($class,, $properties) = Core::configure($this->getFactory($record));
        $object = new $class($properties, $owner ?: $this);
        $this->afterFactory($object);
        return $object;
    }

    /**
     * Возвращает данные создаваемого объекта
     *
     * @param array $record
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFactory(array $record = []): array
    {
        return $record ? array_replace_recursive($this->_factory, $record) : $this->_factory;
    }

    /**
     * Устанавливает данные создаваемых объектов
     *
     * @param array $factory
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setFactory(array $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * Генерация события выполняемого после создания объекта
     *
     * @param object $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterFactory($object)
    {
        return $this->trigger('onAfterFactory', new GEvent($this, ['target' => $this, 'object' => $object]));
    }
}
