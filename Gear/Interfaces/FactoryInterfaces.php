<?php

namespace Gear\Interfaces;

/**
 * Интерфейс фабрики объектов
 *
 * @package Gear Framework
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
     * @param array|\Closure $properties
     * @param ObjectInterface|null $owner
     * @return ObjectInterface|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function factory($properties, ?ObjectInterface $owner = null): ?ObjectInterface;

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getFactoryProperties(array $properties = []): array;
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
    public static function factory($properties, ?ObjectInterface $owner = null): ?ObjectInterface;

    /**
     * Возвращает параметры по-умолчанию создаваемых объектов
     *
     * @param array $properties
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public static function getFactoryProperties(array $properties = []): array;
}
