<?php

namespace gear\interfaces;

/**
 * Интерфейс сервисов (модуль, компонент, плагин)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IService
{
    /**
     * Установка сервиса
     *
     * @param array|string|\Closure $config
     * @param array|string|\Closure $properties
     * @param IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function install($config = [], $properties = [], \gear\interfaces\IObject $owner = null): IService;

    /**
     * Инициализация сервиса
     *
     * @param array|string|\Closure $config
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = []);

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string|\Closure $properties
     * @param IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function it($properties = [], \gear\interfaces\IObject $owner = null): IService;
}

/**
 * Интерфейс модулей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IModule extends IService {}

/**
 * Интерфейс компонентов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IComponent extends IService {}

/**
 * Интерфейс плагинов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IPlugin extends IService {}

interface IHelper
{
    public function __call(string $name, array $arguments);
}
