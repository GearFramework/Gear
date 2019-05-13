<?php

namespace Gear\Traits\Factory;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use gear\library\GEvent;

/**
 * Методы статической фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait StaticFactoryTrait
{
    /**
     * Генерация события после создания объекта
     *
     * @param ObjectInterface $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function afterFactory(ObjectInterface $object)
    {
        return true;
    }

    /**
     * Генерация события перед созданием объекта
     *
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function beforeFactory(array &$properties)
    {
        return true;
    }

    /**
     * @param $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function factory($properties, ObjectInterface $owner = null): ?ObjectInterface
    {
        if ($properties instanceof \Closure) {
            $properties = $properties($owner);
        }
        if (!is_array($properties)) {
            throw self::FactoryInvalidItemPropertiesException();
        }
        $object = null;
        if (static::beforeFactory($properties)) {
            $properties = self::getFactoryProperties($properties);
            list($class, $config, $properties) = Core::configure($properties);
            if (method_exists($class, 'install')) {
                $object = $class::install($config, $properties, $owner);
            } else {
                if ($config) {
                    $class::i($config);
                }
                $object = new $class($properties, $owner);
            }
            self::afterFactory($object);
        }
        return $object;
    }

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getFactoryProperties(array $properties = []): array
    {
        return array_replace_recursive(static::$_factoryProperties, $properties);
    }
}
