<?php

namespace Gear\Traits\Factory;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Library\GEvent;

/**
 * Методы фабрики объектов
 *
 * @package Gear Framework
 *
 * @property array factoryProperties
 * @property array model
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
trait FactoryTrait
{
    /**
     * Генерация события после создания объекта
     *
     * @param ObjectInterface $object
     * @return mixed
     * @since 0.0.1
     * @version 0.0.2
     */
    public function afterFactory(ObjectInterface $object)
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
    public function beforeFactory(array &$properties)
    {
        return $this->trigger('onBeforeFactory', new GEvent($this, ['properties' => &$properties]));
    }

    /**
     * Метод создания объекта
     *
     * @param iterable $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function factory(iterable $properties, ObjectInterface $owner = null): ?ObjectInterface
    {
        if (!$owner) {
            $owner = $this;
        }
        $object = null;
        if ($this->beforeFactory($properties)) {
            $properties = $this->getFactoryProperties($properties);
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
     * Возвращает класс создаваемых фабрикой объектов
     *
     * @param array $properties
     * @return string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getFactoryClass(array $properties = []): ?string
    {
        $class = null;
        $properties = $this->getFactoryProperties($properties);
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
    public function getFactoryProperties(array $properties = []): array
    {
        return array_replace_recursive($this->model, $properties);
    }

    /**
     * Возвращает параметры создаваемой модели
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getModel(): array
    {
        if (is_string($this->_model)) {
            if ($this->_model[0] === '@') {
                $model = Core::model(substr($this->_model, 1));
            } else {
                $model = ['class' => $this->_model];
            }
        } else {
            $model = $this->_model;
        }
        return $model;
    }

    /**
     * Установка параметров создаваемых объектов
     *
     * @param array $properties
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setFactoryProperties(array $properties)
    {
        $this->model = $properties;
    }

    /**
     * Установка параметров создаваемых объектов
     *
     * @param array|string $model
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }
}
