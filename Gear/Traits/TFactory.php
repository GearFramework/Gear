<?php

namespace Gear\Traits;

use Gear\Core;
use Gear\Interfaces\IObject;
use Gear\Library\GEvent;

/**
 * Методы фабрики объектов
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
     * Генерация события после создания объекта
     *
     * @param IObject $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterFactory(IObject $object)
    {
        return $this->trigger('onAfterFactory', new GEvent($this, ['object' => $object]));
    }

    /**
     * Генерация события перед созданием объекта
     *
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeFactory(array $properties)
    {
        return $this->trigger('onBeforeFactory', new GEvent($this, ['properties' => $properties]));
    }

    /**
     * Метод создания объекта
     *
     * @param $properties
     * @param IObject|null $owner
     * @return IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function factory($properties, IObject $owner = null): ?IObject
    {
        if (!$owner) {
            $owner = $this;
        }
        if ($properties instanceof \Closure) {
            $properties = $properties($owner);
        }
        if (!is_array($properties)) {
            throw self::FactoryInvalidItemPropertiesException();
        }
        $object = null;
        if ($this->beforeFactory($properties)) {
            $properties = array_replace_recursive($this->getFactoryProperties($properties), $properties);
            list($class, $config, $properties) = Core::configure($properties);
            if (method_exists($class, 'install')) {
                $object = $class::install($config, $properties, $owner);
            } else {
                if ($config) {
                    $class::i($config);
                }
                $object = new $class($properties, $owner);
            }
            $this->afterFactory($object);
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
    public function getFactoryProperties(array $properties): array
    {
        return $this->_factoryProperties ?? [];
    }

    /**
     * Установка параметров создаваемых объектов
     *
     * @param $properties
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setFactoryProperties($properties)
    {
        if ($properties instanceof \Closure) {
            $properties = $properties($this);
        }
        if (!is_array($properties)) {
            throw self::FactoryInvalidItemPropertiesException();
        }
        $this->_factoryProperties = $properties;
    }
}
