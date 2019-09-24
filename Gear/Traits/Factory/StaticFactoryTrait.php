<?php

namespace Gear\Traits\Factory;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;

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
     * @param iterable $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function factory(iterable $properties, ObjectInterface $owner = null): ?ObjectInterface
    {
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
     * Возвращает класс создаваемых фабрикой объектов
     *
     * @param array $properties
     * @return string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getFactoryClass(array $properties = []): ?string
    {
        $class = null;
        $properties = static::getFactoryProperties($properties);
        if (isset($properties['class'])) {
            $class = is_array($properties['class']) ? $properties['class']['name'] : $properties['class'];
        }
        return $class;
    }

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function getFactoryProperties(array $properties = []): array
    {
        return array_replace_recursive(static::getModel(), $properties);
    }

    /**
     * Возвращает параметры создаваемой модели
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getModel(): array
    {
        $model = [];
        if (is_string(static::$_model)) {
            if (static::$_model[0] === '@') {
                $model = Core::model(substr(static::$_model, 1));
            }
        } else {
            $model = static::$_model;
        }
        return $model;
    }
}
