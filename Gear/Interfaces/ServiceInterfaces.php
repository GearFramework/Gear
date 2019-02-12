<?php

namespace Gear\Interfaces;

/**
 * Интерфейс компонентов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ComponentInterface extends ServiceInterface {}

/**
 * Интерфейс хелперов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface HelperInterface
{
    /**
     * Обработка и выполнение вызываемого метода
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.2
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments);

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

/**
 * Интерфейс модулей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ModuleInterface extends ServiceInterface {}

/**
 * Интерфейс плагинов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface PluginInterface extends ServiceInterface {}

/**
 * Интерфейс сервисов (модуль, компонент, плагин)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface ServiceInterface
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
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function install($config = [], $properties = [], ?ObjectInterface $owner = null): ServiceInterface;

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string $properties
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function it($properties = [], ?ObjectInterface $owner = null): ServiceInterface;

    /**
     * Деинсталляция сервиса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall();
}
