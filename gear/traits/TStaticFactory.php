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
trait TStaticFactory
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
    public static function factory(array $record = [], $owner = null)
    {
        list($class,, $properties) = Core::configure(self::getFactory($record));
        $object = new $class($properties, $owner);
        self::afterFactory($object, $owner);
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
    public static function getFactory(array $record = []): array
    {
        return $record ? array_replace_recursive(static::$_factory, $record) : static::$_factory;
    }

    /**
     * Устанавливает данные создаваемых объектов
     *
     * @param array $factory
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function setFactory(array $factory)
    {
        static::$_factory = $factory;
    }

    /**
     * Генерация события выполняемого после создания объекта
     *
     * @param object $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function afterFactory($object, $owner)
    {
        $result = true;
        if ($owner) {
            $result = $owner->trigger('onAfterFactory', new GEvent($owner, ['target' => $owner, 'object' => $object]));
        }
        return $result;
    }
}
