<?php

namespace Gear\Interfaces;

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
     * Инициализация сервиса
     *
     * @param array|string $config
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = []): bool;

    /**
     * Установка сервиса
     *
     * @param array|string $config
     * @param array|string $properties
     * @param IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function install($config = [], $properties = [], IObject $owner = null): IService;

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string $properties
     * @param IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function it($properties = [], IObject $owner = null): IService;

    /**
     * Деинсталляция сервиса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall();
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

/**
 * Интерфейс хелперов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IHelper
{
    /**
     * Запуск метода хелпера
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments);
}
