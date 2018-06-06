<?php

namespace Gear\Interfaces;

/**
 * Интерфейс фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IFactory
{
    /**
     * Метод фабрики создания объектов
     *
     * @param $properties
     * @param IObject|null $owner
     * @return IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function factory($properties, ?IObject $owner = null): ?IObject;
}

/**
 * Интерфейс статической фабрики объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IStaticFactory
{
    /**
     * Метод фабрики создания объектов
     *
     * @param $properties
     * @param IObject|null $owner
     * @return IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function factory($properties, ?IObject $owner = null): ?IObject;
}
