<?php

namespace Gear\Interfaces;

/**
 * Интерфейс фабрики объектов
 *
 * @package Gear Framework
 *
 * @property string|null factoryClass
 * @property array|string factoryProperties
 * @property array|string model
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface FactoryInterface
{
    /**
     * Метод создания объекта
     *
     * @param iterable $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function factory(iterable $properties, ?ObjectInterface $owner = null): ?ObjectInterface;

    /**
     * Возвращает класс создаваемых фабрикой объектов
     *
     * @param array $properties
     * @return string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getFactoryClass(array $properties = []): ?string;

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getFactoryProperties(array $properties = []): array;

    /**
     * Возвращает параметры создаваемой модели
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getModel(): array;
}

/**
 * Интерфейс статической фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface StaticFactoryInterface
{
    /**
     * Метод создания объекта
     *
     * @param array|\Closure $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function factory(iterable $properties, ObjectInterface $owner = null): ?ObjectInterface;

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getFactoryProperties(array $properties = []): array;

    /**
     * Возвращает параметры создаваемой модели
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getModel(): array;
}
